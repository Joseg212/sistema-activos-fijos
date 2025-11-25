<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251125142604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activofijo DROP FOREIGN KEY clase_actf');
        $this->addSql('ALTER TABLE activofijo DROP FOREIGN KEY ubic_af');
        $this->addSql('DROP INDEX ubic_af ON activofijo');
        $this->addSql('DROP INDEX ix_id_clase ON activofijo');
        $this->addSql('ALTER TABLE activofijo DROP valors, DROP dep_acum, DROP valor_venta, DROP tiempo_vu, DROP tip_unid, CHANGE code_activof code_activof VARCHAR(6) DEFAULT NULL, CHANGE fecha_compra fecha_compra DATETIME DEFAULT NULL, CHANGE distribuidor distribuidor VARCHAR(120) NOT NULL, CHANGE costo costo NUMERIC(12, 2) DEFAULT NULL, CHANGE impuesto impuesto NUMERIC(12, 2) DEFAULT NULL, CHANGE costo_total costo_total NUMERIC(12, 2) DEFAULT NULL, CHANGE costo_flete costo_flete NUMERIC(12, 2) DEFAULT NULL, CHANGE num_serie num_serie VARCHAR(30) DEFAULT NULL, CHANGE estatus estatus VARCHAR(15) DEFAULT NULL, CHANGE marca marca TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE amortizaciones DROP FOREIGN KEY rlt_activf_amortiz');
        $this->addSql('DROP INDEX ix_activf_amortiz ON amortizaciones');
        $this->addSql('ALTER TABLE amortizaciones CHANGE id_af id_af VARCHAR(30) NOT NULL, CHANGE mes mes VARCHAR(2) NOT NULL, CHANGE anio anio VARCHAR(4) NOT NULL, CHANGE periodo periodo INT NOT NULL, CHANGE costo_hist costo_hist NUMERIC(12, 2) NOT NULL, CHANGE factor_correc factor_correc NUMERIC(15, 6) NOT NULL, CHANGE reconversion reconversion NUMERIC(30, 2) NOT NULL');
        $this->addSql('ALTER TABLE clasificacion CHANGE descripcion descripcion VARCHAR(120) NOT NULL');
        $this->addSql('ALTER TABLE fact_mejora DROP FOREIGN KEY rel_mant_detmej');
        $this->addSql('DROP INDEX ix_id_mant_i ON fact_mejora');
        $this->addSql('ALTER TABLE fact_mejora CHANGE id_mant id_mant VARCHAR(25) NOT NULL, CHANGE proveedor proveedor VARCHAR(160) NOT NULL, CHANGE proveedor_rif proveedor_rif VARCHAR(20) NOT NULL, CHANGE estatus estatus VARCHAR(1) NOT NULL');
        $this->addSql('ALTER TABLE fact_mejoratmp CHANGE id_mant id_mant VARCHAR(35) NOT NULL, CHANGE proveedor proveedor VARCHAR(160) NOT NULL, CHANGE proveedor_rif proveedor_rif VARCHAR(20) NOT NULL, CHANGE estatus estatus VARCHAR(1) NOT NULL');
        $this->addSql('ALTER TABLE finiquito DROP FOREIGN KEY rel_activof_finiquito');
        $this->addSql('DROP INDEX IX_Relationship2 ON finiquito');
        $this->addSql('ALTER TABLE finiquito CHANGE id_af id_af VARCHAR(30) NOT NULL, CHANGE tipo_finiquito tipo_finiquito VARCHAR(30) NOT NULL, CHANGE fecha_finiquito fecha_finiquito DATETIME DEFAULT NULL, CHANGE observacion observacion LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE indice_precio CHANGE anio anio VARCHAR(4) NOT NULL, CHANGE mes mes VARCHAR(2) NOT NULL, CHANGE factor factor NUMERIC(15, 6) NOT NULL, CHANGE reconver reconver NUMERIC(15, 2) NOT NULL');
        $this->addSql('ALTER TABLE mantenimiento DROP FOREIGN KEY actvf_resp');
        $this->addSql('ALTER TABLE mantenimiento DROP FOREIGN KEY resp_mant');
        $this->addSql('DROP INDEX ix_resp_m ON mantenimiento');
        $this->addSql('DROP INDEX ix_af_m ON mantenimiento');
        $this->addSql('ALTER TABLE mantenimiento CHANGE id_mant id_mant VARCHAR(30) NOT NULL, CHANGE id_af id_af VARCHAR(30) NOT NULL, CHANGE id_resp id_resp VARCHAR(20) NOT NULL, CHANGE tipo_mant tipo_mant VARCHAR(25) NOT NULL, CHANGE fecha_fact fecha_fact DATETIME DEFAULT NULL, CHANGE telefono_prov telefono_prov VARCHAR(25) DEFAULT NULL, CHANGE banco banco VARCHAR(150) NOT NULL, CHANGE tipo_doc tipo_doc VARCHAR(35) NOT NULL, CHANGE numero_doc numero_doc VARCHAR(20) NOT NULL, CHANGE unidad_tiempo unidad_tiempo VARCHAR(25) NOT NULL, CHANGE numero_tiempo numero_tiempo INT NOT NULL, CHANGE detalle detalle LONGTEXT NOT NULL, CHANGE si_traslado si_traslado TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX idx_orden_niveñ ON opcion_menu');
        $this->addSql('ALTER TABLE opcion_menu CHANGE id_opcion id_opcion VARCHAR(30) NOT NULL, CHANGE nivel nivel INT DEFAULT NULL, CHANGE orden orden VARCHAR(10) DEFAULT NULL, CHANGE opcion opcion VARCHAR(50) DEFAULT NULL, CHANGE Grupo grupo VARCHAR(15) DEFAULT NULL, CHANGE ruta ruta VARCHAR(50) DEFAULT NULL, CHANGE icono icono VARCHAR(30) DEFAULT NULL, CHANGE estatus estatus VARCHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE permiso_menu DROP FOREIGN KEY user_permm');
        $this->addSql('ALTER TABLE permiso_menu DROP FOREIGN KEY opcion_permm');
        $this->addSql('DROP INDEX ix_id_opcion_pm ON permiso_menu');
        $this->addSql('DROP INDEX ix_idus_pm ON permiso_menu');
        $this->addSql('ALTER TABLE permiso_menu CHANGE id_permiso id_permiso VARCHAR(30) NOT NULL, CHANGE id_opcion id_opcion VARCHAR(30) DEFAULT NULL, CHANGE permiso permiso VARCHAR(12) DEFAULT NULL');
        $this->addSql('ALTER TABLE propiedad CHANGE tipo tipo VARCHAR(50) NOT NULL, CHANGE nombre nombre VARCHAR(120) DEFAULT NULL, CHANGE encargado encargado VARCHAR(120) DEFAULT NULL, CHANGE direccion direccion VARCHAR(150) DEFAULT NULL, CHANGE nota nota VARCHAR(150) DEFAULT NULL, CHANGE telefono telefono VARCHAR(30) DEFAULT NULL, CHANGE movil movil VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE responsable CHANGE id_resp id_resp VARCHAR(30) NOT NULL, CHANGE estatus estatus VARCHAR(25) NOT NULL');
        $this->addSql('ALTER TABLE tipo_amortiz DROP FOREIGN KEY rlc_activf_tipoa');
        $this->addSql('DROP INDEX ix_activf_tipa ON tipo_amortiz');
        $this->addSql('ALTER TABLE tipo_amortiz CHANGE formula formula VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE traslado DROP FOREIGN KEY af_tra');
        $this->addSql('DROP INDEX af_tra ON traslado');
        $this->addSql('ALTER TABLE traslado CHANGE id_traslado id_traslado VARCHAR(25) NOT NULL, CHANGE id_af id_af VARCHAR(25) NOT NULL, CHANGE email_user email_user VARCHAR(120) NOT NULL, CHANGE tipo_des tipo_des VARCHAR(100) NOT NULL, CHANGE id_resp_emisor id_resp_emisor VARCHAR(25) NOT NULL, CHANGE id_resp_destino id_resp_destino VARCHAR(25) NOT NULL, CHANGE id_ubic_orig id_ubic_orig VARCHAR(25) NOT NULL, CHANGE id_ubic_dest id_ubic_dest VARCHAR(25) DEFAULT NULL, CHANGE destino_externo destino_externo TINYINT(1) NOT NULL, CHANGE destino_externo_ubic destino_externo_ubic LONGTEXT DEFAULT NULL, CHANGE destino_externo_info destino_externo_info LONGTEXT DEFAULT NULL, CHANGE fec_recep fec_recep DATETIME DEFAULT NULL, CHANGE motivo motivo LONGTEXT NOT NULL, CHANGE observ observ LONGTEXT DEFAULT NULL, CHANGE estatus estatus VARCHAR(15) NOT NULL');
        $this->addSql('ALTER TABLE ubicacion DROP FOREIGN KEY prop_ubic');
        $this->addSql('DROP INDEX prop_ubic ON ubicacion');
        $this->addSql('ALTER TABLE ubicacion CHANGE id_ubic id_ubic VARCHAR(30) NOT NULL, CHANGE nota nota VARCHAR(160) DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario CHANGE id_us id_us VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE first_name first_name VARCHAR(120) DEFAULT NULL, CHANGE last_name last_name VARCHAR(120) DEFAULT NULL, CHANGE telf_movil telf_movil VARCHAR(20) DEFAULT NULL, CHANGE telf_local telf_local VARCHAR(20) DEFAULT NULL, CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE status status VARCHAR(20) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2265B05DE7927C74 ON usuario (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activofijo ADD valors NUMERIC(12, 2) NOT NULL, ADD dep_acum NUMERIC(12, 2) NOT NULL, ADD valor_venta NUMERIC(12, 2) NOT NULL, ADD tiempo_vu INT NOT NULL, ADD tip_unid VARCHAR(10) NOT NULL, CHANGE code_activof code_activof VARCHAR(10) DEFAULT NULL, CHANGE fecha_compra fecha_compra DATETIME NOT NULL, CHANGE distribuidor distribuidor VARCHAR(40) NOT NULL, CHANGE costo costo NUMERIC(12, 2) NOT NULL, CHANGE impuesto impuesto NUMERIC(10, 2) NOT NULL, CHANGE costo_total costo_total NUMERIC(12, 2) NOT NULL, CHANGE costo_flete costo_flete NUMERIC(12, 2) NOT NULL, CHANGE num_serie num_serie VARCHAR(30) NOT NULL, CHANGE estatus estatus VARCHAR(15) NOT NULL, CHANGE marca marca TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE activofijo ADD CONSTRAINT clase_actf FOREIGN KEY (id_clase) REFERENCES clasificacion (id_clase) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activofijo ADD CONSTRAINT ubic_af FOREIGN KEY (id_ubic) REFERENCES ubicacion (id_ubic) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX ubic_af ON activofijo (id_ubic)');
        $this->addSql('CREATE INDEX ix_id_clase ON activofijo (id_clase)');
        $this->addSql('ALTER TABLE amortizaciones CHANGE id_af id_af VARCHAR(30) DEFAULT NULL, CHANGE mes mes CHAR(2) NOT NULL, CHANGE anio anio CHAR(4) NOT NULL, CHANGE periodo periodo INT DEFAULT 0 NOT NULL, CHANGE factor_correc factor_correc NUMERIC(25, 6) NOT NULL, CHANGE costo_hist costo_hist NUMERIC(14, 2) DEFAULT NULL, CHANGE reconversion reconversion NUMERIC(30, 0) NOT NULL');
        $this->addSql('ALTER TABLE amortizaciones ADD CONSTRAINT rlt_activf_amortiz FOREIGN KEY (id_af) REFERENCES activofijo (id_af) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX ix_activf_amortiz ON amortizaciones (id_af)');
        $this->addSql('ALTER TABLE clasificacion CHANGE descripcion descripcion VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE fact_mejora CHANGE id_mant id_mant VARCHAR(25) DEFAULT NULL, CHANGE proveedor proveedor VARCHAR(150) NOT NULL, CHANGE proveedor_rif proveedor_rif VARCHAR(15) DEFAULT NULL, CHANGE estatus estatus CHAR(1) DEFAULT \'P\' NOT NULL');
        $this->addSql('ALTER TABLE fact_mejora ADD CONSTRAINT rel_mant_detmej FOREIGN KEY (id_mant) REFERENCES mantenimiento (id_mant)');
        $this->addSql('CREATE INDEX ix_id_mant_i ON fact_mejora (id_mant)');
        $this->addSql('ALTER TABLE fact_mejoratmp CHANGE id_mant id_mant VARCHAR(35) DEFAULT NULL, CHANGE proveedor proveedor VARCHAR(150) NOT NULL, CHANGE proveedor_rif proveedor_rif VARCHAR(15) DEFAULT NULL, CHANGE estatus estatus CHAR(1) DEFAULT \'P\' NOT NULL');
        $this->addSql('ALTER TABLE finiquito CHANGE fecha_finiquito fecha_finiquito DATETIME NOT NULL, CHANGE id_af id_af VARCHAR(30) DEFAULT NULL, CHANGE tipo_finiquito tipo_finiquito VARCHAR(30) DEFAULT \'Por Obsoleto\' NOT NULL, CHANGE observacion observacion TEXT DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE finiquito ADD CONSTRAINT rel_activof_finiquito FOREIGN KEY (id_af) REFERENCES activofijo (id_af) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IX_Relationship2 ON finiquito (id_af)');
        $this->addSql('ALTER TABLE indice_precio CHANGE anio anio INT NOT NULL, CHANGE mes mes VARCHAR(20) NOT NULL, CHANGE factor factor NUMERIC(35, 6) NOT NULL COMMENT \'Factor de Correcion aplicado al valuo\', CHANGE reconver reconver NUMERIC(15, 2) NOT NULL COMMENT \'Silve para la reconversión monetaria\'');
        $this->addSql('ALTER TABLE mantenimiento CHANGE id_mant id_mant VARCHAR(25) NOT NULL, CHANGE id_af id_af VARCHAR(30) DEFAULT NULL, CHANGE id_resp id_resp VARCHAR(20) DEFAULT NULL, CHANGE tipo_mant tipo_mant VARCHAR(25) DEFAULT \'Reparacion\' NOT NULL, CHANGE fecha_fact fecha_fact DATETIME NOT NULL, CHANGE telefono_prov telefono_prov VARCHAR(25) NOT NULL, CHANGE unidad_tiempo unidad_tiempo VARCHAR(25) DEFAULT \'Hotas\' NOT NULL, CHANGE numero_tiempo numero_tiempo INT DEFAULT 0 NOT NULL, CHANGE detalle detalle TEXT NOT NULL, CHANGE si_traslado si_traslado TINYINT(1) DEFAULT 0 NOT NULL, CHANGE banco banco VARCHAR(150) DEFAULT NULL, CHANGE tipo_doc tipo_doc VARCHAR(35) DEFAULT NULL, CHANGE numero_doc numero_doc VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE mantenimiento ADD CONSTRAINT actvf_resp FOREIGN KEY (id_af) REFERENCES activofijo (id_af)');
        $this->addSql('ALTER TABLE mantenimiento ADD CONSTRAINT resp_mant FOREIGN KEY (id_resp) REFERENCES responsable (id_resp)');
        $this->addSql('CREATE INDEX ix_resp_m ON mantenimiento (id_resp)');
        $this->addSql('CREATE INDEX ix_af_m ON mantenimiento (id_af)');
        $this->addSql('ALTER TABLE opcion_menu CHANGE id_opcion id_opcion VARCHAR(20) NOT NULL, CHANGE nivel nivel INT NOT NULL, CHANGE orden orden VARCHAR(10) NOT NULL, CHANGE opcion opcion VARCHAR(50) NOT NULL, CHANGE grupo Grupo VARCHAR(15) NOT NULL, CHANGE ruta ruta VARCHAR(50) NOT NULL, CHANGE icono icono VARCHAR(30) NOT NULL, CHANGE estatus estatus CHAR(1) DEFAULT \'A\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX idx_orden_niveñ ON opcion_menu (orden, nivel)');
        $this->addSql('ALTER TABLE permiso_menu CHANGE id_permiso id_permiso VARCHAR(20) NOT NULL, CHANGE permiso permiso VARCHAR(12) DEFAULT \'NotActivo\' NOT NULL, CHANGE id_opcion id_opcion VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE permiso_menu ADD CONSTRAINT user_permm FOREIGN KEY (id_us) REFERENCES usuario (id_us) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE permiso_menu ADD CONSTRAINT opcion_permm FOREIGN KEY (id_opcion) REFERENCES opcion_menu (id_opcion)');
        $this->addSql('CREATE INDEX ix_id_opcion_pm ON permiso_menu (id_opcion)');
        $this->addSql('CREATE INDEX ix_idus_pm ON permiso_menu (id_us)');
        $this->addSql('ALTER TABLE propiedad CHANGE tipo tipo VARCHAR(40) DEFAULT \'Oficina\' NOT NULL, CHANGE nombre nombre VARCHAR(35) NOT NULL, CHANGE encargado encargado VARCHAR(25) NOT NULL, CHANGE direccion direccion VARCHAR(35) NOT NULL, CHANGE nota nota VARCHAR(35) NOT NULL, CHANGE telefono telefono VARCHAR(15) NOT NULL, CHANGE movil movil VARCHAR(15) NOT NULL');
        $this->addSql('ALTER TABLE responsable CHANGE id_resp id_resp VARCHAR(20) NOT NULL, CHANGE estatus estatus VARCHAR(25) DEFAULT \'Activo\' NOT NULL');
        $this->addSql('ALTER TABLE tipo_amortiz CHANGE formula formula VARCHAR(30) DEFAULT \'MÉTODO DECRECIENTE \' NOT NULL');
        $this->addSql('ALTER TABLE tipo_amortiz ADD CONSTRAINT rlc_activf_tipoa FOREIGN KEY (id_af) REFERENCES activofijo (id_af) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX ix_activf_tipa ON tipo_amortiz (id_af)');
        $this->addSql('ALTER TABLE traslado CHANGE id_traslado id_traslado VARCHAR(18) NOT NULL, CHANGE id_af id_af VARCHAR(30) NOT NULL, CHANGE email_user email_user VARCHAR(120) DEFAULT NULL, CHANGE tipo_des tipo_des VARCHAR(100) DEFAULT NULL, CHANGE id_resp_emisor id_resp_emisor VARCHAR(20) DEFAULT \' \' NOT NULL, CHANGE id_resp_destino id_resp_destino VARCHAR(20) NOT NULL, CHANGE id_ubic_orig id_ubic_orig VARCHAR(20) DEFAULT \' \' NOT NULL, CHANGE id_ubic_dest id_ubic_dest VARCHAR(20) DEFAULT \' \' NOT NULL, CHANGE destino_externo destino_externo TINYINT(1) DEFAULT 0 NOT NULL, CHANGE destino_externo_ubic destino_externo_ubic TEXT DEFAULT NULL, CHANGE destino_externo_info destino_externo_info TEXT DEFAULT NULL, CHANGE fec_recep fec_recep DATETIME NOT NULL, CHANGE motivo motivo TEXT DEFAULT \' \' NOT NULL, CHANGE observ observ TEXT DEFAULT \' \' NOT NULL, CHANGE estatus estatus VARCHAR(15) DEFAULT \' \' NOT NULL');
        $this->addSql('ALTER TABLE traslado ADD CONSTRAINT af_tra FOREIGN KEY (id_af) REFERENCES activofijo (id_af) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX af_tra ON traslado (id_af)');
        $this->addSql('ALTER TABLE ubicacion CHANGE id_ubic id_ubic VARCHAR(20) NOT NULL, CHANGE nota nota VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE ubicacion ADD CONSTRAINT prop_ubic FOREIGN KEY (id_propiedad) REFERENCES propiedad (id_propiedad) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX prop_ubic ON ubicacion (id_propiedad)');
        $this->addSql('DROP INDEX UNIQ_2265B05DE7927C74 ON usuario');
        $this->addSql('ALTER TABLE usuario CHANGE id_us id_us INT AUTO_INCREMENT NOT NULL, CHANGE email email VARCHAR(160) NOT NULL, CHANGE roles roles TEXT NOT NULL, CHANGE password password VARCHAR(150) NOT NULL, CHANGE first_name first_name VARCHAR(120) NOT NULL, CHANGE last_name last_name VARCHAR(120) NOT NULL, CHANGE telf_movil telf_movil VARCHAR(20) NOT NULL, CHANGE telf_local telf_local VARCHAR(20) NOT NULL, CHANGE status status VARCHAR(25) NOT NULL');
    }
}
