 <div class="mt-4 flex flex-col items-center justify-center gap-4 px-4 sm:flex-row">
     <x-button type="submit" typeButton="primary" icon="file-symlink" text="Generar documento" class="w-full sm:w-auto"
         name="action" value="generate" />
     <x-button type="submit" name="action" value="draft"
         text="{{ isset($dte['id']) ? 'Actualizar' : 'Guardar como' }} borrador" typeButton="secondary"
         class="w-full sm:w-auto" icon="save" />
 </div>
