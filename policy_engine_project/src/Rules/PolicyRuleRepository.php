<?php
declare(strict_types=1);
namespace App\Rules;
use App\Domain\PolicyRule;
final class PolicyRuleRepository
{
    /** @return PolicyRule[] */
    public function all(): array
    {
        return [
            new PolicyRule('R001','Données critiques interdites sur modèle public',['data_classification'=>['critical'],'model_type'=>['public_llm']],'block',['use_private_model','security_review'],['GDPR','ISO27001'],1000),
            new PolicyRule('R002','Finance + modèle public => tokenisation obligatoire',['business_unit'=>['Finance'],'model_type'=>['public_llm']],'tokenize',['mask_sensitive_fields','log_event'],['GDPR','ISO27001'],900),
            new PolicyRule('R003','RH + données confidentielles => masquage obligatoire',['business_unit'=>['HR'],'data_classification'=>['confidential','critical']],'mask',['mask_sensitive_fields','retain_audit_trail'],['GDPR'],850),
            new PolicyRule('R004','Pays UE + données confidentielles sur modèle public => revue',['country'=>['BE','FR','DE','NL','LU'],'data_classification'=>['confidential'],'model_type'=>['public_llm']],'review',['dpo_review','document_justification'],['GDPR','NIS2'],800),
            new PolicyRule('R005','NIS2 + modèle public + données non publiques => revue sécurité',['frameworks'=>['NIS2'],'model_type'=>['public_llm'],'data_classification'=>['confidential','critical']],'review',['security_review','incident_traceability'],['NIS2','ISO27001'],780),
            new PolicyRule('R006','ISO 27001 + secrets ou données critiques sur API externe/public => blocage',['frameworks'=>['ISO27001'],'data_classification'=>['critical'],'model_type'=>['public_llm','external_api']],'block',['rotate_credentials','security_incident_record'],['ISO27001'],950),
            new PolicyRule('R007','Données publiques sur modèle privé => autoriser',['data_classification'=>['public'],'model_type'=>['private_llm','internal_rag']],'allow',['log_event'],[],100),
            new PolicyRule('R008','Pays hors UE ciblés + modèle public + données confidentielles => blocage',['country'=>['US','CN','IN','BR'],'data_classification'=>['confidential','critical'],'model_type'=>['public_llm']],'block',['cross_border_assessment'],['GDPR'],920),
        ];
    }
}
