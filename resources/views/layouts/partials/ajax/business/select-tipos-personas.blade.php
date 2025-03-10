  <x-select name="tipo_persona" id="tipo_de_persona" label="Tipo de persona" value="{{ $tipo_persona }}"
      selected="{{ $tipo_persona }}" :options="['1' => 'Natural', '2' => 'Jurídica']" :search="false" />
