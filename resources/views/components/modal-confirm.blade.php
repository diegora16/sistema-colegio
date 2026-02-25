{{--
    Modal de confirmación para eliminar.
    Uso:
        <x-modal-confirm
            id="modal-eliminar-{{ $item->id }}"
            title="Eliminar registro"
            message="¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer."
            action="{{ route('ruta.destroy', $item) }}"
        />
        <button @click="$dispatch('open-modal', 'modal-eliminar-{{ $item->id }}')">Eliminar</button>
--}}
@props(['id', 'title' => 'Confirmar acción', 'message' => '¿Estás seguro?', 'action', 'btnText' => 'Eliminar'])

<div x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail === '{{ $id }}') open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>

    {{-- Card --}}
    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-6 z-10"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
            </div>
            <h3 class="text-gray-800 font-semibold text-base">{{ $title }}</h3>
        </div>

        <p class="text-gray-600 text-sm mb-6">{{ $message }}</p>

        <div class="flex justify-end gap-3">
            <button type="button"
                    @click="open = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                Cancelar
            </button>
            <form method="POST" action="{{ $action }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                    <i class="fa-solid fa-trash-can mr-1"></i>
                    {{ $btnText }}
                </button>
            </form>
        </div>
    </div>
</div>
