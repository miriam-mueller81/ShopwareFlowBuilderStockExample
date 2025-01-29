<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1738140579AddMailTemplateTypeGeneral extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1738140579;
    }

    public function update(Connection $connection): void
    {
        $this->createGeneralMailTemplateType($connection);
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    protected function createGeneralMailTemplateType(Connection $connection): void
    {
        $mailTemplateTypeId = Uuid::randomBytes();
        $langEN = $this->findLanguageIdByCode($connection, 'en-GB');
        $langDE = $this->findLanguageIdByCode($connection, 'de-DE');

        $connection->insert('mail_template_type', [
            'id' => $mailTemplateTypeId,
            'technical_name' => 'custom.general',
            'available_entities' => json_encode(['product' => 'product', 'category' => 'category', 'order' => 'order', 'customer' => 'customer']),
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $connection->insert('mail_template_type_translation', [
            'mail_template_type_id' => $mailTemplateTypeId,
            'language_id' => $langEN,
            'name' => 'General mail template type',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $connection->insert('mail_template_type_translation', [
            'mail_template_type_id' => $mailTemplateTypeId,
            'language_id' => $langDE,
            'name' => 'Allgemeiner Mail Template Typ',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    protected function findLanguageIdByCode(Connection $connection, string $locale): string
    {
        $query = <<<SQL
SELECT `language`.`id`
FROM `language`
INNER JOIN `locale` ON `locale`.`id` = `language`.`locale_id`
WHERE `locale`.`code` = :code
SQL;
        return $connection->fetchOne($query, ['code' => $locale]);
    }
}