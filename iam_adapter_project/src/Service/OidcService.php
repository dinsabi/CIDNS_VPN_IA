<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\User;
use App\Infrastructure\HttpClient;

final class OidcService
{
    public function __construct(
        private readonly array $oidcConfig,
        private readonly array $groupRoleMap,
        private readonly HttpClient $httpClient,
        private readonly UserSegmentationService $segmentationService,
    ) {
    }

    public function buildAuthorizationUrl(string $state): string
    {
        $params = [
            'client_id' => $this->oidcConfig['client_id'],
            'redirect_uri' => $this->oidcConfig['redirect_uri'],
            'response_type' => 'code',
            'scope' => $this->oidcConfig['scopes'],
            'state' => $state,
        ];

        return $this->oidcConfig['authorization_endpoint'] . '?' . http_build_query($params);
    }

    public function handleCallback(string $code): User
    {
        $tokenResponse = $this->httpClient->postForm($this->oidcConfig['token_endpoint'], [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->oidcConfig['client_id'],
            'client_secret' => $this->oidcConfig['client_secret'],
            'redirect_uri' => $this->oidcConfig['redirect_uri'],
        ]);

        $accessToken = (string)($tokenResponse['access_token'] ?? '');
        $userinfo = $this->httpClient->getJson(
            $this->oidcConfig['userinfo_endpoint'],
            ['Authorization: Bearer ' . $accessToken]
        );

        return $this->mapClaimsToUser($userinfo);
    }

    public function buildDemoUser(string $username): User
    {
        $fixtures = [
            'alice' => [
                'sub' => 'user-alice',
                'email' => 'alice@company.example',
                'name' => 'Alice HR Manager',
                'department' => 'HR',
                'country' => 'BE',
                'employment_type' => 'employee',
                'clearance' => 'standard',
                'groups' => ['grp-hr-manager'],
            ],
            'bob' => [
                'sub' => 'user-bob',
                'email' => 'bob@company.example',
                'name' => 'Bob IT Admin',
                'department' => 'IT',
                'country' => 'BE',
                'employment_type' => 'employee',
                'clearance' => 'elevated',
                'groups' => ['grp-it-admin', 'grp-security-architect'],
            ],
            'carol' => [
                'sub' => 'user-carol',
                'email' => 'carol@company.example',
                'name' => 'Carol Finance Analyst',
                'department' => 'Finance',
                'country' => 'BE',
                'employment_type' => 'employee',
                'clearance' => 'standard',
                'groups' => ['grp-finance-analyst'],
            ],
        ];

        if (!isset($fixtures[$username])) {
            throw new \RuntimeException('Unknown demo user');
        }

        return $this->mapClaimsToUser($fixtures[$username]);
    }

    private function mapClaimsToUser(array $claims): User
    {
        $groups = is_array($claims['groups'] ?? null) ? $claims['groups'] : [];
        $roles = [];

        foreach ($groups as $group) {
            if (isset($this->groupRoleMap[$group])) {
                $roles[] = $this->groupRoleMap[$group];
            }
        }

        $roles = array_values(array_unique($roles));
        $department = (string)($claims['department'] ?? 'Unknown');

        return new User(
            subject: (string)($claims['sub'] ?? ''),
            email: (string)($claims['email'] ?? ''),
            displayName: (string)($claims['name'] ?? ''),
            roles: $roles,
            attributes: [
                'department' => $department,
                'country' => (string)($claims['country'] ?? 'BE'),
                'employment_type' => (string)($claims['employment_type'] ?? 'employee'),
                'clearance' => (string)($claims['clearance'] ?? 'standard'),
                'groups' => $groups,
            ],
            segment: $this->segmentationService->segmentFromDepartment($department),
        );
    }
}
