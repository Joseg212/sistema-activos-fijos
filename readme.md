# 🏢 Sistema de Gestión de Activos Fijos

## 📋 Información del Sistema

Sistema desarrollado en **Symfony** para el control y gestión integral de activos fijos across múltiples propiedades. 

### ✨ Funcionalidades Principales

- 🏠 **Gestión de Propiedades** - Registro y administración de diferentes propiedades
- 📊 **Métodos de Amortización** - Configuración de diversos métodos de depreciación
- 🧮 **Cálculo de Depreciación** - Cálculo automático de depreciación acumulada
- 🔄 **Traslados entre Propiedades** - Movimiento de activos entre diferentes ubicaciones
- 📈 **Resúmenes Contables** - Reportes financieros a fecha específica
- 💹 **Ajuste Inflacionario** - Cálculo y aplicación de ajustes por inflación
- 📋 **Inventario Completo** - Control detallado de todos los activos

---
![Estructura del proyecto](bin/portada1.png)
---
![Estructura del proyecto](bin/portada2.png)
---
![Estructura del proyecto](bin/portada3.png)
---
## ⚙️ Requerimientos del Sistema

### 🛠 Stack Tecnológico Requerido

| Componente | Versión | Estado |
|------------|---------|--------|
| ⚡ **Symfony** | 5.4.* | ✅ Requerido |
| 🐘 **PHP** | 8.2 | ✅ Requerido |
| 📦 **Composer** | Latest | ✅ Requerido |
| 🗄 **MySQL** | 5.4 | ✅ Requerido |
| 🔄 **Migraciones** | No disponible | ❌ No soportado |

## 🚀 Instalación y Configuración

### 📥 Descarga del Repositorio

```bash
git clone https://github.com/Joseg212/sistema-activos-fijos.git
cd sistema-activos-fijos
```
### 📀 Hay un scripts para instalar la base datos

El archivo de se llama appactivofijo.sql con el mismo podrá instalar la base datos, aunque puede intentar instalar la base datos usando el proceso de migrations, pero no estoy seguro de funcione al 100% ya que acostumbre aplicar cambios directamente. 

### 🤔 En el futuro dejare esa mala practica. 
---
### 🔧 Instalación de Dependencias

```bash
# Instalación optimizada para producción
composer install --no-dev --optimize-autoloader
```

### ⚙️ Configuración de Base de Datos

1. **Crear base de datos:**
```sql
CREATE DATABASE appactivofijo;
```

2. **Ejecutar scripts de base de datos:**
```bash
# Los scripts SQL se encuentran en la carpeta /database
mysql -u usuario -p appactivofijo < database/scripts/appactivofijo.sql
```

### 🔒 Configuración de Variables de Entorno

Crear archivo `.env.local` con la configuración:

```env
DATABASE_URL="mysql://usuario:password@localhost:3306/appactivofijo?charset=utf8mb4"
APP_ENV=prod 
# dev si es ambiente de desarrollo
APP_SECRET=tu_clave_secreta_aqui
```

### 🎯 Configuración Final

```bash
# Limpiar cache de producción
php bin/console cache:clear --env=prod

# Opcional: cargar datos iniciales
php bin/console doctrine:fixtures:load --env=prod
```

## 📁 Estructura del Proyecto

```
sistema-activos-fijos/
├── bin/
├── config/             # 🎮 Configuración del sistema
├── migrations/
├── src/
│   ├── Controller/      # 🎮 Controladores
│   ├── Entity/          # 🗂 Entidades de BD
│   ├── Repository/      # 📚 Repositorios
│   ├── Form/            # 📄 Repositorios
│   └── ClassPrivate/    # 🔧 Clases de uso datos.
├── templates/           # 🎨 Vistas Twig
├── database/           # 🗃 Scripts de base de datos
└── public/             # 🌐 Archivos públicos
```

## 🗄 Base de Datos

### 📊 Tablas Principales

- `propiedad` - Registro de propiedades
- `ubicacion` - Registro de las ubicaciones
- `activosfijo` - Inventario de activos fijos
- `amortizaciones` - Amortizaciones aplicadas al activo
- `mantenimiento` - Historico de mantenimiento de los activos
- `depreciaciones` - Histórico de depreciaciones
- `traslados` - Registro de movimientos entre propiedades
- `entre otros`

## 🆘 Soporte y Contacto

### 📞 Canales de Ayuda

Si encuentras algún problema durante la instalación o necesitas asistencia:

- 📧 **Email:** [jghernández053@gmail.com](mailto:jghernández053@gmail.com)
- 👨‍💼 **Contacto Técnico:** Ingeniero José Hernández

### ⚠️ Importante

> **🔒 Anti-Spam:** Por favor, evite que su correo sea marcado como spam para poder responderle adecuadamente. Incluya un asunto claro como "[Sistema Activos] Consulta Instalación".

## 🎉 ¡Listo!

Una vez completados todos los pasos, tu sistema de gestión de activos fijos estará listo para usar. 

Si quiere enviar alguna donación para seguir trabajando en buenos sistemas a mi cuenta paypal jghernandez053@gmail.com. Quiero realizar buenos desarrollos pero necesito de una buena motivación 😄.

**✨ ¡Gracias por implementar nuestro sistema!**

---

## 📄 Licencia y Términos de Uso
## ⚖️ Licencia del Software
### Este software se distribuye bajo Licencia Propietaria - Todos los derechos reservados.

## 📝 Términos y Condiciones
 🚫 Prohibida la Venta: Este producto no está destinado para fines comerciales de reventa.

 
## 🛠️ Soporte Técnico y Modificaciones

📞 ¿Necesitas Personalizar el Sistema?
¡Estoy aquí para ayudarte! Como desarrollador original del sistema, puedo proporcionarte:

🔧 Soporte técnico especializado

💡 Modificaciones y personalizaciones a medida

🚀 Nuevas funcionalidades específicas para tu negocio

🐛 Corrección de errores y optimizaciones

---

*Última actualización: 25/11/2025*  
*Desarrollado con ❤️ usando Symfony*