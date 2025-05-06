<script setup>
import InputError from '@/components/InputError.vue';
import Modal from '@/components/Modal.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Pencil, Trash } from 'lucide-vue-next';
import { defineComponent, ref } from 'vue';
import { toast } from 'vue3-toastify';

const auth = usePage().props.user;

defineComponent({
    name: 'EntriesIndex',
    components: {
        AppLayout,
    },
});

const props = defineProps({
    entries: Array,
});

const showModal = ref(false);
const dataList = ref([...props.entries]);
const dataProducts = ref([]);
const isEdit = ref(false);
const clientId = ref(null);

const form = useForm({
    product_id: 0,
    quantity: 0,
    total_cost: 0,
    unit_cost: 0,
});

const createEntry = () => {
    if (form.product_id === 0) {
        toast.error('Selecciona un producto', {
            theme: 'colored',
        });
        return;
    }
    if (form.quantity <= 0) {
        toast.error('La cantidad debe ser mayor a 0', {
            theme: 'colored',
        });
        return;
    }
    if (form.total_cost <= 0) {
        toast.error('El costo total debe ser mayor a 0', {
            theme: 'colored',
        });
        return;
    }
    if (form.unit_cost <= 0) {
        toast.error('El costo por unidad debe ser mayor a 0', {
            theme: 'colored',
        });
        return;
    }

    axios
        .post(route('entries.store'), form)
        .then((response) => {
            toast.success('Ingreso creado con éxito', {
                theme: 'colored',
            });
            dataList.value.unshift(response.data.data);
            showModal.value = false;
            form.reset();
        })
        .catch((error) => {
            if (error.response.status === 422) {
                form.setErrors(error.response.data.errors);
            } else {
                toast.error('Error al crear el ingreso');
            }
        });
};

const showEditModal = (product) => {
    isEdit.value = true;
    getProducts();
    showModal.value = true;
    form.product_id = product.product.id;
    form.quantity = product.quantity;
    form.total_cost = product.total_cost;
    form.unit_cost = product.unit_cost;
    clientId.value = product.id;
};

const updateEntry = () => {
    if (form.product_id === 0) {
        toast.error('Selecciona un producto', {
            theme: 'colored',
        });
        return;
    }
    if (form.quantity <= 0) {
        toast.error('La cantidad debe ser mayor a 0', {
            theme: 'colored',
        });
        return;
    }
    if (form.total_cost <= 0) {
        toast.error('El costo total debe ser mayor a 0', {
            theme: 'colored',
        });
        return;
    }
    if (form.unit_cost <= 0) {
        toast.error('El costo por unidad debe ser mayor a 0', {
            theme: 'colored',
        });
        return;
    }

    axios
        .put(route('entries.update', clientId.value), form)
        .then((response) => {
            if (response.status === 200) {
                toast.success('Ingreso actualizado con éxito', {
                    theme: 'colored',
                });
                const index = dataList.value.findIndex((client) => client.id === clientId.value);
                dataList.value[index] = form.data();
                showModal.value = false;
                form.reset();
            } else {
                toast.error('Error al actualizar el ingreso');
            }
        })
        .catch((error) => {
            if (error.response.status === 422) {
                form.setErrors(error.response.data.errors);
            } else {
                toast.error('Error al actualizar el ingreso');
            }
        });
};

const deleteEntry = (id) => {
    axios
        .delete(route('entries.destroy', { id }))
        .then((response) => {
            if (response.status === 200) {
                toast.success('Ingreso eliminado con éxito', {
                    theme: 'colored',
                });
                dataList.value = dataList.value.filter((client) => client.id !== id);
            } else {
                toast.error('Error al eliminar el ingreso');
            }
        })
        .catch((error) => {
            toast.error('Error al eliminar el ingreso');
        });
};

const generateUnitCost = () => {
    const totalCost = form.total_cost;
    const quantity = form.quantity;
    if (quantity > 0) {
        form.unit_cost = Math.ceil(totalCost / quantity).toFixed(2);
    } else {
        form.unit_cost = 0;
    }
};

const openModal = () => {
    showModal.value = true;
    isEdit.value = false;
    getProducts();
    form.reset();
};

const getProducts = () => {
    axios.get(route('products.showAllProducts')).then((response) => {
        if (response.status === 200) {
            dataProducts.value = response.data.data;
        } else {
            toast.error('Error al obtener los productos', {
                theme: 'colored',
            });
        }
    });
};
</script>
<template>
    <Head title="Ingresos" />
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tus Ingresos</h1>
                <button class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600" @click="openModal">Generar Ingreso</button>
            </div>
            <div class="overflow-x-auto rounded-lg shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 border shadow-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Cantidad</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Costo Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                Costo por Unidad
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                Usuario Creador
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                Fecha de Entrada
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                        <tr v-for="client in dataList" :key="client.id" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">{{ client.product.name }}</td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">{{ client.quantity }}</td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">{{ client.total_cost }}</td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">{{ client.unit_cost }}</td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">{{ client.user }}</td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">{{ new Date(client.entry_date).toLocaleDateString() }}</td>
                            <td class="flex space-x-2 px-6 py-4 whitespace-nowrap">
                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-blue-500 px-4 py-2 text-sm font-medium text-blue-600 shadow-sm transition hover:bg-blue-500 hover:text-white"
                                    @click="showEditModal(client)"
                                >
                                    <Pencil class="h-4 w-4" />
                                    Editar
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-red-500 px-4 py-2 text-sm font-medium text-red-600 shadow-sm transition hover:bg-red-500 hover:text-white"
                                    @click="deleteEntry(client.id)"
                                >
                                    <Trash class="h-4 w-4" />
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="dataList.length === 0">
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No hay ingresos disponibles.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Modal :show="showModal" maxWidth="md">
                <template #default>
                    <div class="flex flex-col gap-4 p-6">
                        <h2 class="text-lg font-bold" v-if="isEdit">Editar Ingreso</h2>
                        <h2 class="text-lg font-bold" v-else>Crear Ingreso</h2>
                        <form @submit.prevent="isEdit ? updateEntry() : createEntry()">
                            <div class="mb-4">
                                <Label for="product_id">Producto</Label>
                                <select
                                    v-model="form.product_id"
                                    class="focus:ring-opacity-50 mt-1 block w-full rounded-md border-gray-300 bg-white p-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                >
                                    <option value="0" disabled selected class="hidden">Selecciona un producto</option>
                                    <option
                                        v-for="product in dataProducts"
                                        :key="product.id"
                                        :value="product.id"
                                        class="text-gray-900 dark:text-white"
                                    >
                                        {{ product.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.product_id" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <Label for="quantity">Cantidad</Label>
                                <Input
                                    id="quantity"
                                    type="number"
                                    v-model="form.quantity"
                                    class="mt-1 block w-full"
                                    placeholder="Cantidad del producto"
                                />
                                <InputError :message="form.errors.quantity" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <Label for="total_cost">Costo Total</Label>
                                <Input
                                    id="total_cost"
                                    type="number"
                                    v-model="form.total_cost"
                                    class="mt-1 block w-full"
                                    placeholder="Costo total del ingreso"
                                />
                                <InputError :message="form.errors.total_cost" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <Label for="unit_cost">Costo por Unidad</Label>
                                <Input
                                    id="unit_cost"
                                    type="number"
                                    v-model="form.unit_cost"
                                    class="mt-1 block w-full"
                                    placeholder="Costo por unidad del ingreso"
                                />
                                <InputError :message="form.errors.unit_cost" class="mt-2" />
                                <button type="button" class="mt-2 text-sm text-blue-500 hover:underline" @click="generateUnitCost">
                                    Calcular Costo por Unidad
                                </button>
                            </div>
                            <div class="mt-4 flex items-center justify-end">
                                <button
                                    type="button"
                                    class="mr-2 rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600"
                                    @click="showModal = false"
                                >
                                    Cancelar
                                </button>
                                <button type="submit" class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">Guardar</button>
                            </div>
                        </form>
                    </div>
                </template>
            </Modal>
        </div>
    </AppLayout>
</template>
