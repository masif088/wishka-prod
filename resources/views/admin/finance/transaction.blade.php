<x-admin-layout>
    <x-slot name="title">
        Buku Penjualan - Penjualan Berjalan
    </x-slot>

        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <livewire:table.master name="Transaction"/>
            </div>
        </div>

</x-admin-layout>
