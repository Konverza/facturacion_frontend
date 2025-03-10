 @foreach ($business_products as $product)
     <x-tr>
         <x-td>
             {{ $loop->iteration }}
         </x-td>
         <x-td>
             {{ $product->codigo }}
         </x-td>
         <x-td>
             @if ($number !== '01')
                 ${{ $product->precioSinTributos }}
             @else
                 ${{ $product->precioUni }}
             @endif
         </x-td>
         <x-td>
             {{ $product->descripcion }}
         </x-td>
         <x-td>
             {{ $product->stockActual }}
         </x-td>
         <x-td :last="true">
             <form method="POST" action="{{ Route('business.dte.product.select') }}">
                 @csrf
                 <input type="hidden" name="product_id" value="{{ $product->id }}">
                 <x-button type="button" icon="arrow-next" size="small" class="btn-selected-product"
                     typeButton="secondary" text="Seleccionar" />
             </form>
         </x-td>
     </x-tr>
 @endforeach
