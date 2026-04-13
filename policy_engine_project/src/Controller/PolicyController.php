<?php
declare(strict_types=1);
namespace App\Controller;
use App\Application\EvaluatePolicy;
final class PolicyController
{
    public function __construct(private readonly EvaluatePolicy $evaluatePolicy) {}
    public function evaluate(): never
    {
        $payload = json_decode(file_get_contents('php://input') ?: '', true);
        if (!is_array($payload)) \jsonResponse(['error'=>'invalid_json','message'=>'Le body doit être un JSON valide.'], 422);
        \jsonResponse($this->evaluatePolicy->execute($payload), 200);
    }
}
