{{-- DEPRECATED: Use livewire table instead  --}}
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
                 ${{ $product->precioSinTributos }} <br>
                 @if ($product->special_price > 0)
                     <span class="text-success">Con descuento: ${{ $product->special_price }}</span>
                 @endif
             @else
                 ${{ $product->precioUni }} <br>
                 @if ($product->special_price_with_iva > 0)
                     <span class="text-success">Con descuento: ${{ $product->special_price_with_iva }}</span>
                 @endif
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
