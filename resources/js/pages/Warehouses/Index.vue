<script setup>
import InputError from '@/components/InputError.vue';
import Modal from '@/components/Modal.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Eye, Pencil, Trash } from 'lucide-vue-next';
import { defineComponent, ref } from 'vue';
import { toast } from 'vue3-toastify';

const auth = usePage().props.user;

defineComponent({
    name: 'WarehousesIndex',
    components: {
        AppLayout,
    },
});

const props = defineProps({
    warehouses: {
        type: Array,
        required: true,
    },
});


const showModal = ref(false);
const warehouseName = ref(null);
const isEdit = ref(false);
const warehouseId = ref(null);
const dataList = ref([...props.warehouses]);
const showModalDetails = ref(false);
const showModalAddWarehouse = ref(false);

const form = useForm({
    name: '',
    percentage_earnings: 2,
});

const formAddAWarehouse = useForm({
    email: '',
});

const createWarehouse = () => {
    if (!form.name) {
        form.errors.name = 'El campo nombre es obligatorio.';
        return;
    }

    if (form.name.length < 3) {
        form.errors.name = 'El nombre debe tener al menos 3 caracteres.';
        return;
    }

    if (form.name.length > 255) {
        form.errors.name = 'El nombre no puede tener más de 255 caracteres.';
        return;
    }

    if (form.percentage_earnings <= 0) {
        form.errors.percentage_earnings = 'El porcentaje de ganancias no puede ser negativo o cero.';
        form.percentage_earnings = 1;
        return;
    }

    if (form.percentage_earnings > 100) {
        form.errors.percentage_earnings = 'El porcentaje de ganancias no puede ser mayor a 100.';
        return;
    }

    axios
        .post('/warehouses', { name: form.name, percentage_earnings: form.percentage_earnings })
        .then((response) => {
            toast.success('Almacén creado con éxito', {
                theme: 'colored',
            });
            dataList.value.push(response.data.warehouse);
            router.reload();
            closeModal();
        })
        .catch((error) => {
            if (error.response && error.response.data.errors && error.response.data.errors.name) {
                form.reset('name');
                if (warehouseName.value instanceof HTMLInputElement) {
                    warehouseName.value.focus();
                }
            }
        });
};

const showEditModal = (warehouse) => {
    form.name = warehouse.name;
    form.percentage_earnings = warehouse.percentage_earnings;
    warehouseId.value = warehouse.id;
    isEdit.value = true;
    showModal.value = true;
};

const updateWarehouse = () => {
    if (!form.name) {
        form.errors.name = 'El campo nombre es obligatorio.';
        return;
    }

    if (form.name.length < 3) {
        form.errors.name = 'El nombre debe tener al menos 3 caracteres.';
        return;
    }

    if (form.name.length > 255) {
        form.errors.name = 'El nombre no puede tener más de 255 caracteres.';
        return;
    }

    if (form.percentage_earnings <= 0) {
        form.errors.percentage_earnings = 'El porcentaje de ganancias no puede ser negativo o cero.';
        form.percentage_earnings = 1;
        return;
    }

    if (form.percentage_earnings > 100) {
        form.errors.percentage_earnings = 'El porcentaje de ganancias no puede ser mayor a 100.';
        return;
    }

    form.put(route('warehouses.update', warehouseId.value), {
        data: {
            name: form.name,
            percentage_earnings: form.percentage_earnings,
        },
        onSuccess: () => {
            router.reload();
            toast.success('Almacén actualizado con éxito', {
                theme: 'colored',
            });
            dataList.value = dataList.value.map((item) => {
                if (item.id === warehouseId.value) {
                    return { ...item, name: form.name };
                }
                return item;
            });
            closeModal();
        },
        onError: (errors) => {
            if (errors.name) {
                form.reset('name');
                if (warehouseName.value instanceof HTMLInputElement) {
                    warehouseName.value.focus();
                }
            }
        },
    });
};

const deleteWarehouse = (warehouse) => {
    form.delete(route('warehouses.destroy', warehouse.id), {
        onSuccess: () => {
            router.reload();
            toast.success('Almacén eliminado con éxito', {
                theme: 'colored',
            });
            dataList.value = dataList.value.filter((item) => item.id !== warehouse.id);
            closeModal();
        },
        onError: (errors) => {
            if (errors.name) {
                form.reset('name');
                if (warehouseName.value instanceof HTMLInputElement) {
                    warehouseName.value.focus();
                }
            }
        },
    });
};

const showDetailsModal = (warehouse) => {
    warehouseId.value = warehouse.id;
    showModalDetails.value = true;
};

const closeModal = () => {
    showModal.value = false;
    isEdit.value = false;
    warehouseId.value = null;
    form.reset();
};

const addAWarehouse = () => {
    if (!formAddAWarehouse.email) {
        formAddAWarehouse.errors.email = 'El campo email es obligatorio.';
        return;
    }
    axios
        .post(`/warehouses/${warehouseId.value}/users`, { email: formAddAWarehouse.email })
        .then((response) => {
            toast.success('Usuario agregado al almacén con éxito', {
                theme: 'colored',
            });
            dataList.value = dataList.value.map((item) => {
                if (item.id === warehouseId.value) {
                    return { ...item, users: [...item.users, response.data.user] };
                }
                return item;
            });
            showModalAddWarehouse.value = false;
            formAddAWarehouse.reset();
        })
        .catch((error) => {
            if (error.response && error.response.data.errors && error.response.data.errors.email) {
                formAddAWarehouse.reset('email');
            }
        });
};

const deleteUserToWarehouse = (user) => {
    axios
        .delete(`/warehouses/${warehouseId.value}/users/${user.id}`)
        .then(() => {
            toast.success('Usuario eliminado del almacén con éxito', {
                theme: 'colored',
            });
            dataList.value = dataList.value.map((item) => {
                if (item.id === warehouseId.value) {
                    return { ...item, users: item.users.filter((u) => u.id !== user.id) };
                }
                return item;
            });
        })
        .catch((error) => {
            if (error.response && error.response.data.errors && error.response.data.errors.email) {
                formAddAWarehouse.reset('email');
            }
        });
};
</script>
<template>
    <Head title="Almacenes" />
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tus Almacenes</h1>
                <button class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600" @click="showModal = true">Crear Almacén</button>
            </div>
            <div class="overflow-x-auto rounded-lg shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 border shadow-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Creado por</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Creado el</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Porcentaje de Ganancias</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                        <tr v-for="warehouse in dataList" :key="warehouse.id" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.owner.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{
                                    new Date(warehouse.created_at).toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' })
                                }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.percentage_earnings }}%</td>
                            <td class="flex space-x-2 px-6 py-4 whitespace-nowrap" v-if="warehouse.owner.id === auth.id">
                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-green-500 px-4 py-2 text-sm font-medium text-green-600 shadow-sm transition hover:bg-green-500 hover:text-white"
                                    @click="showDetailsModal(warehouse)"
                                >
                                    <Eye class="h-4 w-4" />
                                    Ver Detalles
                                </button>

                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-blue-500 px-4 py-2 text-sm font-medium text-blue-600 shadow-sm transition hover:bg-blue-500 hover:text-white"
                                    @click="showEditModal(warehouse)"
                                >
                                    <Pencil class="h-4 w-4" />
                                    Editar
                                </button>

                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-red-500 px-4 py-2 text-sm font-medium text-red-600 shadow-sm transition hover:bg-red-500 hover:text-white"
                                    v-if="!warehouse.not_deleted"    
                                    @click="deleteWarehouse(warehouse)"
                                >
                                    <Trash class="h-4 w-4" />
                                    Eliminar
                                </button>
                            </td>
                            <td class="gap-4 px-6 py-4 whitespace-nowrap" v-else>
                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-green-500 px-4 py-2 text-sm font-medium text-green-600 shadow-sm transition hover:bg-green-500 hover:text-white"
                                    @click="showDetailsModal(warehouse)"
                                >
                                    <Eye class="h-4 w-4" />
                                    Ver Detalles
                                </button>
                            </td>
                        </tr>
                        <tr v-if="props.warehouses.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center">No hay almacenes disponibles.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Modal :show="showModal" @close="showModal = false" maxWidth="md">
                <template #default>
                    <div class="flex flex-col gap-2 p-6">
                        <h2 class="text-lg font-semibold">{{ isEdit ? 'Editar Almacén' : 'Crear Almacén' }}</h2>
                        <form @submit.prevent="isEdit ? updateWarehouse() : createWarehouse()">
                            <div class="mb-4">
                                <Label for="name">Nombre del Almacén</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    class="mt-1 block w-full"
                                    placeholder="Nombre del Almacén"
                                    ref="warehouseName"
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <Label for="percentage_earnings">Porcentaje de Ganancias</Label>
                                <Input
                                    id="percentage_earnings"
                                    type="number"
                                    v-model="form.percentage_earnings"
                                    class="mt-1 block w-full"
                                    placeholder="Porcentaje de Ganancias"
                                />
                                <InputError :message="form.errors.percentage_earnings" class="mt-2" />
                            </div>
                            <div class="flex items-center justify-end gap-4">
                                <button type="button" class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600" @click="closeModal()">
                                    Cancelar
                                </button>
                                <button type="submit" class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                                    {{ isEdit ? 'Actualizar Almacén' : 'Crear Almacén' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </template>
            </Modal>
            <Modal :show="showModalDetails" @close="showModalDetails = false" maxWidth="md">
                <template #default>
                    <div class="flex flex-col gap-2 p-6">
                        <h2 class="text-lg font-semibold">Detalles del Almacén</h2>
                        <p><strong>Nombre:</strong> {{ dataList.find((item) => item.id === warehouseId)?.name }}</p>
                        <p><strong>Creado por:</strong> {{ dataList.find((item) => item.id === warehouseId)?.owner.name }}</p>
                        <p>
                            <strong>Creado el:</strong>
                            {{
                                new Date(dataList.find((item) => item.id === warehouseId)?.created_at).toLocaleDateString('es-ES', {
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit',
                                })
                            }}
                        </p>
                        <ul class="mt-4 space-y-2">
                            <li class="font-semibold">Usuarios:</li>
                            <li
                                v-for="item in dataList.find((w) => w.id === warehouseId)?.users || []"
                                :key="item.id"
                                class="flex items-center justify-between border-b p-2"
                            >
                                <span>{{ item.name }}</span>
                                <button
                                    class="text-red-500 hover:text-red-700"
                                    @click="deleteUserToWarehouse(item)"
                                    v-if="dataList.find((w) => w.id === warehouseId)?.owner.id === auth.id"
                                >
                                    <Trash class="h-4 w-4" />
                                </button>
                            </li>
                            <li v-if="(dataList.find((w) => w.id === warehouseId)?.users || []).length === 0" class="text-center">
                                No hay usuarios asignados.
                            </li>
                            <button
                                class="mt-4 rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                                @click="showModalAddWarehouse = true"
                                v-if="dataList.find((w) => w.id === warehouseId)?.owner.id === auth.id"
                            >
                                Agregar Usuario
                            </button>
                        </ul>
                        <div class="mt-4 flex items-center justify-end gap-4">
                            <button
                                type="button"
                                class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600"
                                @click="showModalDetails = false"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </template>
            </Modal>
            <Modal :show="showModalAddWarehouse" @close="showModalAddWarehouse = false" maxWidth="md">
                <template #default>
                    <div class="flex flex-col gap-2 p-6">
                        <h2 class="text-lg font-semibold">Agregar Usuario al Almacén</h2>
                        <form @submit.prevent="addAWarehouse()">
                            <div class="mb-4">
                                <Label for="email">Email del Usuario</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    v-model="formAddAWarehouse.email"
                                    class="mt-1 block w-full"
                                    placeholder="Email del Usuario"
                                />
                                <InputError :message="formAddAWarehouse.errors.email" class="mt-2" />
                            </div>
                            <div class="flex items-center justify-end gap-4">
                                <button
                                    type="button"
                                    class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600"
                                    @click="showModalAddWarehouse = false"
                                >
                                    Cancelar
                                </button>
                                <button type="submit" class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">Agregar Usuario</button>
                            </div>
                        </form>
                    </div>
                </template>
            </Modal>
        </div>
    </AppLayout>
</template>
