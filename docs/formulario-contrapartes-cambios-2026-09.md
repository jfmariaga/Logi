# Formulario de Conocimiento — Asociados de Negocio (Contrapartes)

Cambios solicitados por el cliente (audio + documento `public/docs/FORMULARIO DE
CONOCIMIENTO ASOCIADO DE NEGOCIO SAGRILAFT CLIENTES.docx`) e implementados en
septiembre de 2026.

> **Nota:** este formulario es **distinto** al "Formulario de conocimiento de
> colaboradores". Aquí se trabaja sobre el flujo de **contrapartes / terceros**.

## Archivos afectados

| Archivo | Rol |
|---|---|
| `resources/views/livewire/contrapartes/formulario.blade.php` | Vista del formulario (web + impresión/PDF vía `window.print`) |
| `app/Livewire/Contrapartes/Formulario.php` | Componente Livewire principal |
| `app/Livewire/Contrapartes/Login.php` + `resources/views/livewire/contrapartes/login.blade.php` | Ingreso de terceros |
| `app/Livewire/Admin/Terceros.php` | Listado admin de terceros |
| `database/migrations/2026_09_10_130000_make_tipo_nullable_on_terceros_table.php` | Hace `terceros.tipo` nullable |

Los campos del formulario se guardan en formato **clave/valor** (`tercero_formularios`),
por lo que **agregar o quitar campos NO requiere migraciones**. La única migración
nueva es para permitir que el tipo de persona quede "sin definir".

---

## Fase 1 — Renombrados y eliminaciones

- Título de la sección `INFORMACIÓN GENERAL PROVEEDOR - PRODUCTOR` →
  **`INFORMACIÓN GENERAL PROVEEDORES - CLIENTES`**.
- Etiqueta `Nombre Completo` / `Razón Social` → **`Razón Social / Nombre Persona Natural`**.
- Eliminada la fila **`¿Tiene Casa Matriz?` + `Indique cuál`**.
- Eliminado el campo **`Dirección Corporativa`** de la sección *Datos de Ubicación*.
- `REPRESENTANTE LEGAL` → **`REPRESENTANTE LEGAL PRINCIPAL`**.
- `COMPOSICIÓN ACCIONARIA` → **`INFORMACIÓN SOBRE COMPOSICIÓN ACCIONARIA MAYOR AL 5%`**
  (solo renombrado; la sección ya estaba justo debajo del representante legal suplente).
- La sección **`DECLARACIÓN PEP`** se **movió** para quedar inmediatamente **debajo de
  `REPRESENTANTE LEGAL SUPLENTE`** (antes iba después de la sección tributaria).
- `VERIFICACIÓN DE LA INFORMACIÓN` → **`INFORMACIÓN COMPLEMENTARIA`**.
- Tabla **DOCUMENTOS REQUERIDOS**:
  - Fila de certificaciones: `BASC / C-TPAT / OEA` → `BASC / C-TPAT / OEA / **ISO 28000**`.
  - `Fotocopia de cédula` → **`Copia cédula del representante legal`**.
  - Eliminadas las filas: `Procuraduría`, `OFAC`, `Contraloría`,
    `Certificación moneda extranjera o activos virtuales`,
    `Origen de los recursos empleados en las operaciones…`.
- Eliminada por completo la sección **`REQUISITOS ADICIONALES POR TIPO DE PROVEEDOR`**.

Se conservan las secciones `DATOS DE UBICACIÓN` (principal) y
`PAÍSES DE ALTO RIESGO O TRANSACCIONES CON ACTIVOS VIRTUALES`.

---

## Fase 2 — Campos nuevos + tipo de persona en el formulario

### Tipo de persona movido del login al formulario

- Se **eliminó** el selector "Tipo de persona" del **login de terceros**
  (`Login.php` + `login.blade.php`). El tercero se crea **sin `tipo`**.
- Migración `2026_09_10_130000_make_tipo_nullable_on_terceros_table.php`:
  `terceros.tipo` pasa a `enum('juridica','natural') NULL DEFAULT NULL`.
- `app/Livewire/Admin/Terceros.php`: el listado muestra **"Sin definir"** cuando
  `tipo` es `null`.
- Mientras `tipo` es `null` el formulario se comporta como **persona natural**
  (superset de campos y documentos). Al elegir "Jurídica" se recalculan
  campos y documentos obligatorios al vuelo.

### Nuevos campos en "INFORMACIÓN GENERAL PROVEEDORES - CLIENTES"

| Campo | Tipo | Almacenamiento |
|---|---|---|
| **Tipo de persona** (Natural / Jurídica) | radio | `terceros.tipo` (vía `camposTerceros`) |
| **Relación con la empresa** (Proveedor / Cliente) | 2 checkboxes independientes | `tercero_formularios`: `es_proveedor`, `es_cliente` |
| **Cuenta con certificado de seguridad** (BASC / OEA / ISO 28000 / C-TPAT / OTRA) | checkboxes múltiples | `tercero_formularios.certificados_seguridad` (CSV) |
| **¿Cuál? (OTRA)** | texto (se habilita solo si se marca "OTRA") | `tercero_formularios.certificado_seguridad_otra` |

### Documentos de certificado de seguridad dinámicos

- Cada certificado marcado agrega automáticamente su fila en
  **"CARGAR DOCUMENTOS REQUERIDOS"** como documento **obligatorio**:
  `Certificado BASC`, `Certificado OEA`, `Certificado ISO 28000`,
  `Certificado C-TPAT`, `Certificado de seguridad (Otra)`.
- Al **desmarcar** un certificado se elimina su fila y su archivo (si el
  formulario aún no fue enviado).
- Métodos nuevos en `Formulario.php`:
  `documentosCertificadosSeguridad()`, `guardarCertificadosSeguridad()`,
  `sincronizarDocumentosCertificados()`.
- `documentosRequeridos()` fue refactorizado para concatenar los certificados
  seleccionados y **ya no incluye `Formulario firmado`**.
- En `guardar()`, cuando cambia el campo `tipo` se ejecuta
  `inicializarDocumentos()` + `calcularProgreso()`.

---

## Fase 3 — Ajustes de este lote

- Título del formulario `FORMULARIO DE REGISTRO DE ASOCIADOS DE NEGOCIOS` →
  **`FORMULARIO DE CONOCIMIENTO ASOCIADO DE NEGOCIO`**.
- Etiqueta `Dirección` → **`Dirección empresa`** en la sección general.
- `Tipo de identificación`: solo quedan **`CC`** y **`NIT`** (se quitaron
  CE, TI, PASAPORTE, NUIP, PPT).
- Eliminada la sección **`DATOS DE UBICACIÓN – VERIFICACIÓN`** y sus campos
  obligatorios (`pais_verificacion`, `departamento_verificacion`,
  `ciudad_verificacion`) de `getCamposObligatorios()`.

---

## Fase 4 — Firma digital (reemplaza el flujo físico)

El flujo anterior era: completar → imprimir el formulario → firmarlo y poner huella
a mano → subir el PDF "Formulario firmado" → enviar. Se reemplazó por firma digital
dentro del formulario (igual que el formulario de colaboradores).

### Vista

- **Eliminado**: el aviso "⚠ Aún no puede descargar el formulario…", el botón
  "Imprimir formulario para firma", la hoja de firma/huella física y el bloque
  de éxito "El formulario firmado ya fue cargado".
- **Nueva sección `FIRMA DEL REPRESENTANTE LEGAL`**: `<canvas>` para firmar con
  mouse/touch, botones *Limpiar* y *Guardar firma*. Una vez guardada muestra la
  imagen con botón *Rehacer firma*. Al lado: nombre, "Representante Legal" y
  tipo + número de documento del representante.
- **Nueva barra única "Cumplimiento del diligenciamiento del formulario"** al
  final, con checklist:
  - Formulario diligenciado: `%`
  - Documentos obligatorios: `Completos` / `Pendientes`
  - Firma del representante legal: `Registrada` / `Pendiente`
- Botón **Enviar formulario** habilitado solo si:
  `progreso >= 100` **y** `documentosCompletos()` **y** `yaFirmado()`.
- La barra y los botones se ocultan en `@media print`; la firma (imagen) sí sale
  en la impresión/PDF.
- JS de dibujo del canvas agregado en `@push('scripts')`
  (`window._firmaContraparte` con `limpiar()` / `guardar()`), sin dependencias
  externas. `guardar()` hace `Livewire.dispatch('setFirmaDibujo', { firma })`.

### Componente

- `enviarFormulario()`: se quitó la exigencia del documento "Formulario firmado";
  ahora valida progreso + documentos obligatorios + `yaFirmado()`.
- `puedeFirmar()`: reactivado (estaba comentado y `guardarFirmaDibujo()` lo
  invocaba → habría dado *fatal error*). Devuelve
  `!yaEnviado() && modo !== 'auditoria'`.
- `puedeEnviar()`: nuevo, encapsula la condición del botón Enviar.
- `guardarFirmaDibujo()`: ahora **borra la firma digital previa** (registro +
  archivo) antes de crear la nueva y limpia `firmaDibujo`. El archivo se nombra
  con timestamp para evitar caché.
- `mount()` → `limpiarFormularioFirmadoLegado()`: elimina de terceros existentes
  el documento heredado "Formulario firmado" (registro + archivo en disco).
- La firma se guarda como `TerceroFirma` tipo `digital` en
  `storage/app/public/documentos_contrapartes/{identificacion}/`.

---

## Fix: previsualización de firma y adjuntos rotos en local (Windows)

**Síntoma:** en local no se veían ni la firma ni los "Ver archivo" (`/storage/...`
daba 404).

**Causa:** `php artisan storage:link` ejecutado desde Git-Bash sobre Windows creó
un **symlink estilo MSYS** que Apache/Laragon no puede seguir.

**Solución:** se reemplazó `public/storage` por un **junction NTFS real**:

```powershell
Remove-Item public\storage -Force
New-Item -ItemType Junction -Path public\storage -Target storage\app\public
```

- Las URLs del blade quedan con `Storage::url(...)` → generan `/storage/...`
  relativo (consistente con el resto de la app, funciona en cualquier host local).
- A la imagen de la firma se le agregó `?v={updated_at->timestamp}` como
  *cache-buster* para que "Rehacer firma" muestre el trazo nuevo.
- El proyecto vive dentro de **Dropbox**: si Dropbox rompe el junction al
  sincronizar, hay que recrearlo con el comando de arriba.
- En **producción (Linux)** esto no aplica: `php artisan storage:link` normal
  funciona bien.

---

## Deploy a producción

Ejecutar, además de subir el código:

```bash
php artisan migrate --force
php artisan storage:link          # si aún no existe el symlink
php artisan view:clear
php artisan config:clear
```

La migración solo hace `terceros.tipo` nullable (rápida, sin pérdida de datos).
Los terceros existentes conservan su `tipo` actual.

---

## Pendiente / deuda técnica menor

- CSS muerto inofensivo en `formulario.blade.php`: `.huella-box`, `.aviso-descarga`.
- Métodos sin uso en `Formulario.php`: `puedeImprimirFormulario()`,
  `formularioFirmadoCargado()`, y la rama `casa_matriz` dentro de `guardar()`.
- Filas antiguas en `tercero_formularios` de campos eliminados
  (`cual_casa_matriz`, `direccion_corporativa`, `*_verificacion`) quedan como
  datos huérfanos; no afectan el cálculo de progreso (solo se cuentan los campos
  vigentes de `getCamposObligatorios()`).
- El **audio** del cliente no se transcribió (sin herramienta disponible en el
  entorno); todos los cambios provienen del documento Word y de las aclaraciones
  posteriores del usuario.
