<script setup lang="ts">
import Modal from '@/components/Modal.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type SharedData } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { ChevronsUpDown, Warehouse } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

const page = usePage<SharedData>();

const selectedWarehouse = computed({
    get: () => usePage().props.selectedWarehouse as Warehouse | null,
    set: (value: Warehouse | null) => {},
});

const dropdownTrigger = ref<HTMLElement | null>(null);
const showSelectedWarehouse = ref(false);
const { isMobile, state } = useSidebar();
interface Warehouse {
    id: number;
    name: string;
}

const warehouses = ref<Warehouse[]>([]);

const getWarehouses = () => {
    axios
        .get('/warehouses/show')
        .then((response) => {
            const data = response.data;
            warehouses.value = data;

            const selected = warehouses.value.find((warehouse) => warehouse.id === selectedWarehouse.value?.id);
            if (!selected) {
                showSelectedWarehouse.value = true;
            } else {
                showSelectedWarehouse.value = false;
            }
        })
        .catch((error) => {
            console.error('Error fetching warehouses:', error);
        });
};

const setSelectedWarehouse = (warehouse: Warehouse) => {
    axios
        .post('/select-warehouse', { id: warehouse.id, name: warehouse.name })
        .then((response) => {
            const data = response.data;
            router.reload({ only: ['selectedWarehouse'] });
            dropdownTrigger.value?.blur();
            selectedWarehouse.value = warehouse;
        })
        .catch((error) => {
            console.error('Error setting selected warehouse:', error);
        });
};

const openModal = () => {
    showSelectedWarehouse.value = true;
    getWarehouses();
};

watch(
    () => selectedWarehouse.value,
    (newVal) => {
        if (!newVal) {
            openModal();
        } else {
            showSelectedWarehouse.value = false;
        }
    },
    { immediate: true },
);


onMounted(() => {
    if (!selectedWarehouse.value) {
        showSelectedWarehouse.value = true;
    } else {
        showSelectedWarehouse.value = false;
    }
    getWarehouses();
});
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        ref="dropdownTrigger"
                        size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <span class="flex items-center gap-2">
                            <Warehouse class="size-4" />
                            {{ selectedWarehouse ? selectedWarehouse.name : 'Seleccionar Almacén' }}
                        </span>
                        <ChevronsUpDown class="ml-auto size-4" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                    :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"
                    align="end"
                    :side-offset="4"
                >
                    <div class="flex flex-col gap-1">
                        <template v-if="warehouses.length > 0">
                            <template v-for="warehouse in warehouses" :key="warehouse.id">
                                <DropdownMenuItem>
                                    <button
                                        @click="setSelectedWarehouse(warehouse)"
                                        ref="dropdownTrigger"
                                        class="text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground flex w-full items-center gap-2 px-4 py-2 text-left text-sm"
                                    >
                                        <Warehouse class="size-4" />
                                        {{ warehouse.name }}
                                    </button>
                                </DropdownMenuItem>
                            </template>
                        </template>
                        <template v-else>
                            <div class="text-sidebar-foreground px-4 py-2 text-sm">No hay almacenes disponibles</div>
                        </template>
                    </div>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
    <Modal :show="showSelectedWarehouse" maxWidth="md">
        <template #default>
            <div class="flex flex-col gap-2 p-6">
                <div class="flex flex-col gap-4">
                    <h2 class="text-lg font-semibold">Seleccionar Almacén</h2>
                    <p class="text-muted-foreground text-sm">Selecciona un almacén para continuar. Puedes cambiarlo más tarde en la configuración.</p>
                    <div class="flex flex-col gap-2">
                        <template v-if="warehouses.length > 0">
                            <template v-for="warehouse in warehouses" :key="warehouse.id">
                                <button
                                    @click="
                                        setSelectedWarehouse(warehouse);
                                        showSelectedWarehouse = false;
                                    "
                                    class="text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground flex w-full items-center gap-2 rounded-md px-4 py-2 text-left text-sm"
                                >
                                    <Warehouse class="size-4" />
                                    {{ warehouse.name }}
                                </button>
                            </template>
                        </template>
                        <template v-else>
                            <div class="text-sidebar-foreground px-4 py-2 text-sm">No hay almacenes disponibles</div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>
