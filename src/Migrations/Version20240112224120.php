<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240112224120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affiliation CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE person person CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE organization organization CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE department department CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE agent CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE annotation CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE concept concept CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE castor_entity CHANGE study_id study_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE castor_institute CHANGE study_id study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE castor_record CHANGE study_id study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE castor_user CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE catalog CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE fdp fdp CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE catalogs_datasets CHANGE catalog_id catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE dataset_id dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE catalogs_studies CHANGE catalog_id catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE study_id study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_dictionary CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_dictionary_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_dictionary_option_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_dictionary_option_option CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE option_group option_group CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_dictionary_variable CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_dictionary_version CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model_module CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model_node CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model_node_external CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model_node_internal CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE data_type data_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:XsdDataType)\'');
        $this->addSql('ALTER TABLE data_model_node_record CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE data_type data_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:XsdDataType)\'');
        $this->addSql('ALTER TABLE data_model_predicate CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE data_model data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model_prefix CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE data_model data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model_triple CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE subject subject CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE predicate predicate CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE object object CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_model_version CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_dependency CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE group_id group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_dependency_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_dependency_rule CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE element element CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_element CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE version version CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE option_group option_group CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE groupId groupId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_elementgroup CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE groupId groupId CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE version version CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE dependencies dependencies CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_mappings CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE study study CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE version version CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_mappings_element CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE element element CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE element_mapping_castor_entity CHANGE element_mapping_id element_mapping_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_mappings_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE groupId groupId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE data_specification_version CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE data_specification data_specification CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE dataset CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE study_id study_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE department CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE organization organization CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE dataset_id dataset_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_api user_api CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution_contactpoint CHANGE distribution_id distribution_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE agent_id agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution_contents CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE distribution distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE dependencies dependencies CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE data_specification data_specification CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE data_specification_version data_specification_version CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution_csv CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution_databases CHANGE distribution distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user user TEXT NOT NULL, CHANGE password password TEXT NOT NULL');
        $this->addSql('ALTER TABLE distribution_dependency CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE group_id group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution_dependency_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution_dependency_rule CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE node node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE distribution_rdf CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE fdp CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE log_generation_distribution CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE distribution distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE log_generation_distribution_record CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE study study CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE log log CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE log_sparql_query CHANGE queried_on queried_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE metadata CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE title title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE description description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_publishers CHANGE metadata_id metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE agent_id agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE dataset_contacts CHANGE metadata_id metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE agent_id agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_catalog CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE catalog catalog CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_catalog_themetaxonomies CHANGE catalog_metadata_id catalog_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE ontology_concept_id ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_dataset CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE dataset dataset CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE keyword keyword CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_dataset_themes CHANGE dataset_metadata_id dataset_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE ontology_concept_id ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_distribution CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE distribution distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_fdp CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE fdp fdp CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_study CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE study_id study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE studied_condition studied_condition CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE intervention intervention CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE keyword keyword CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_study_conditions CHANGE study_metadata_id study_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE ontology_concept_id ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_study_centers CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE metadata metadata CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE organization organization CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_study_centers_departments CHANGE participating_center_id participating_center_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE department_id department_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE metadata_study_team CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE metadata metadata CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE person person CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE ontology CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE ontology_concept CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE ontology ontology CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE orcid_user CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE organization CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE permission_catalog CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE catalog_id catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE permission_data_specification CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE data_specification_id data_specification_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE permission_dataset CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE dataset_id dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE permission_distribution CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE distribution_id distribution_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE permission_distribution_contents CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE distribution_contents_id distribution_contents_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE person CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE study CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE study_castor CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE text_coded CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE text_localized CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE text_localized_item CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE parent parent CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE person_id person_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_api CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE client_id client_id TEXT NOT NULL, CHANGE client_secret client_secret TEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affiliation CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE person person CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE organization organization CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE department department CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE agent CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE annotation CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE concept concept CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE castor_entity CHANGE study_id study_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE castor_institute CHANGE study_id study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE castor_record CHANGE study_id study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE castor_user CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE catalog CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE fdp fdp CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE catalogs_datasets CHANGE catalog_id catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE dataset_id dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE catalogs_studies CHANGE catalog_id catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE study_id study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_dictionary CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_dictionary_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_dictionary_option_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_dictionary_option_option CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE option_group option_group CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_dictionary_variable CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_dictionary_version CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_module CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_node CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_node_external CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_node_internal CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_node_literal CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_node_record CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_node_value CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE data_type data_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE data_model_predicate CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE data_model data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_prefix CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE data_model data_model CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_triple CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE subject subject CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE predicate predicate CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE object object CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_model_version CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_dependency CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE group_id group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_dependency_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_dependency_rule CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE element element CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_element CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE version version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE option_group option_group CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE groupId groupId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_elementgroup CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE groupId groupId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE version version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE dependencies dependencies CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_mappings CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE study study CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE version version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_mappings_element CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE element element CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_mappings_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE groupId groupId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_specification_version CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE data_specification data_specification CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE dataset CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE study_id study_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE dataset_contacts CHANGE metadata_id metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE agent_id agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE department CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE organization organization CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE dataset_id dataset_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE user_api user_api CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_contactpoint CHANGE distribution_id distribution_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE agent_id agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_contents CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE distribution distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE dependencies dependencies CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE data_specification data_specification CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE data_specification_version data_specification_version CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_csv CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_databases CHANGE distribution distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE user user MEDIUMTEXT NOT NULL, CHANGE password password MEDIUMTEXT NOT NULL');
        $this->addSql('ALTER TABLE distribution_dependency CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE group_id group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_dependency_group CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_dependency_rule CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE node node CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE distribution_rdf CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE element_mapping_castor_entity CHANGE element_mapping_id element_mapping_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE fdp CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE log_generation_distribution CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE distribution distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE log_generation_distribution_record CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE study study CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE log log CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE log_sparql_query CHANGE queried_on queried_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE metadata CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE title title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE description description CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_catalog CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE catalog catalog CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_catalog_themetaxonomies CHANGE catalog_metadata_id catalog_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE ontology_concept_id ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_dataset CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE dataset dataset CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE keyword keyword CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_dataset_themes CHANGE dataset_metadata_id dataset_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE ontology_concept_id ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_distribution CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE distribution distribution CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_fdp CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE fdp fdp CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_publishers CHANGE metadata_id metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE agent_id agent_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE study_id study_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE studied_condition studied_condition CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE intervention intervention CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE keyword keyword CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study_centers CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE metadata metadata CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE organization organization CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study_centers_departments CHANGE participating_center_id participating_center_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE department_id department_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study_conditions CHANGE study_metadata_id study_metadata_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE ontology_concept_id ontology_concept_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE metadata_study_team CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE metadata metadata CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE person person CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE ontology CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE ontology_concept CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE ontology ontology CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE orcid_user CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE organization CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE permission_catalog CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE catalog_id catalog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE permission_data_specification CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE data_specification_id data_specification_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE permission_dataset CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE dataset_id dataset_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE permission_distribution CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE distribution_id distribution_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE permission_distribution_contents CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE distribution_contents_id distribution_contents_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE person CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE study CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE study_castor CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE text_coded CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE text_localized CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE text_localized_item CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE parent parent CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE user CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE person_id person_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE user_api CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE client_id client_id MEDIUMTEXT NOT NULL, CHANGE client_secret client_secret MEDIUMTEXT NOT NULL');
    }
}
