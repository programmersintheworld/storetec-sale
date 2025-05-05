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
    name: 'SalesIndex',
    components: {
        AppLayout,
    },
});

const props = defineProps({
    sales: Array,
});

const showModal = ref(false);
const dataList = ref([...props.sales]);
const products = ref([]);
const clients = ref([]);
const isEdit = ref(false);
const saleId = ref(null);

const form = useForm({
    product_id: null,
    client_id: null,
    quantity: 0,
    price: 0,
});
console.log(props.sales);

const createSale = () => {
    if (form.product_id === null || form.client_id === null) {
        toast.error('Por favor selecciona un producto y un cliente', {
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
    if (form.price <= 0) {
        toast.error('El precio debe ser mayor a 0', {
            theme: 'colored',
        });
        return;
    }
    if (form.quantity > form.stock) {
        toast.error(`No hay suficiente stock disponible. Stock: ${form.stock}`, {
            theme: 'colored',
        });
        return;
    }

    axios
        .post(route('sales.store'), form)
        .then((response) => {
            if (response.status === 201) {
                toast.success('Venta creada con éxito', {
                    theme: 'colored',
                });
                console.log(response.data.data);
                dataList.value.unshift(response.data.data);
                showModal.value = false;
                form.reset();
            } else {
                toast.error('Error al crear la venta');
            }
        })
        .catch((error) => {
            if (error.response.status === 422) {
                form.setErrors(error.response.data.errors);
            } else {
                toast.error('Error al crear la venta');
            }
        });
};

const showEditModal = (product) => {
    isEdit.value = true;
    getCustomers();
    getProducts();
    showModal.value = true;
    form.product_id = product.product.id;
    form.client_id = product.client.id;
    form.quantity = product.quantity;
    form.price = product.unit_price;
    saleId.value = product.id;
};

const updateSale = () => {
    if (form.product_id === null || form.client_id === null) {
        toast.error('Por favor selecciona un producto y un cliente', {
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

    if (form.price <= 0) {
        toast.error('El precio debe ser mayor a 0', {
            theme: 'colored',
        });
        return;
    }

    if (form.quantity > form.stock) {
        toast.error(`No hay suficiente stock disponible. Stock: ${form.stock}`, {
            theme: 'colored',
        });
        return;
    }

    axios
        .put(route('sales.update', saleId.value), form)
        .then((response) => {
            if (response.status === 200) {
                toast.success('Venta actualizada con éxito', {
                    theme: 'colored',
                });
                console.log(response.data.data);
                const index = dataList.value.findIndex((sale) => sale.id === saleId.value);
                dataList.value[index] = response.data.data;
                showModal.value = false;
                form.reset();
            } else {
                toast.error('Error al actualizar la venta');
            }
        })
        .catch((error) => {
            if (error.response.status === 422) {
                form.setErrors(error.response.data.errors);
            } else {
                toast.error('Error al actualizar la venta');
            }
        });
};

const deleteSale = (id) => {
    axios
        .delete(route('sales.destroy', { id }))
        .then((response) => {
            if (response.status === 200) {
                toast.success('Venta eliminada con éxito', {
                    theme: 'colored',
                });
                const index = dataList.value.findIndex((sale) => sale.id === id);
                dataList.value.splice(index, 1);
            } else {
                toast.error('Error al eliminar la venta');
            }
        })
        .catch((error) => {
            toast.error('Error al eliminar la venta');
        });
};

const openModal = () => {
    showModal.value = true;
    getCustomers();
    getProducts();
};

const getCustomers = () => {
    axios
        .get(route('customers.showAllClients'))
        .then((response) => {
            if (response.status === 200) {
                clients.value = response.data.data;
            } else {
                toast.error('Error al obtener los clientes');
            }
        })
        .catch((error) => {
            toast.error('Error al obtener los clientes');
        });
};

const getProducts = () => {
    axios
        .get(route('products.showAllProductsPrices'))
        .then((response) => {
            if (response.status === 200) {
                products.value = response.data.data;
            } else {
                toast.error('Error al obtener los productos');
            }
        })
        .catch((error) => {
            toast.error('Error al obtener los productos');
        });
};

const getPrice = () => {
    const product = products.value.find((product) => product.id === form.product_id);
    if (product.stock < form.quantity) {
        toast.error(`No hay suficiente stock disponible. Stock: ${product.stock}`, {
            theme: 'colored',
        });
        form.quantity = product.stock;
    }

    if (product) {
        form.price = Math.ceil(product.sale_price * form.quantity).toFixed(2);
    } else {
        form.price = 0;
    }
};
</script>
<template>
    <Head title="Ventas" />
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tus Ventas</h1>
                <button class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600" @click="openModal">Crear Venta</button>
            </div>
            <div class="overflow-x-auto rounded-lg shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 border shadow-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Producto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Cliente</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Vendedor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Cantidad</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                Precio por unidad
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Fecha</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                        <tr v-for="client in dataList" :key="client.id" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">{{ client.product.name }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ client.client.name }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ client.user }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ client.quantity }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ Math.ceil(client.unit_price / client.quantity).toFixed(2) }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ client.unit_price }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ new Date(client.sold_at).toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' }) }}
                            </td>

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
                                    @click="deleteSale(client.id)"
                                >
                                    <Trash class="h-4 w-4" />
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="dataList.length === 0">
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No hay ventas disponibles.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Modal :show="showModal" maxWidth="md">
                <template #default>
                    <div class="flex flex-col gap-4 p-6">
                        <h2 class="text-lg font-bold" v-if="isEdit">Editar Venta</h2>
                        <h2 class="text-lg font-bold" v-else>Crear Venta</h2>
                        <form @submit.prevent="isEdit ? updateSale() : createSale()">
                            <div class="mb-4">
                                <Label for="product_id">Producto</Label>
                                <select
                                    v-model="form.product_id"
                                    class="focus:ring-opacity-50 mt-1 block w-full rounded-md border-gray-300 bg-white p-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                >
                                    <option value="0" disabled selected class="hidden">Selecciona un producto</option>
                                    <option v-for="product in products" :key="product.id" :value="product.id">
                                        {{ product.name }} - ${{ Math.ceil(product.sale_price).toFixed(2) }} - Stock: {{ product.stock }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.product_id" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <Label for="client_id">Cliente</Label>
                                <select
                                    v-model="form.client_id"
                                    class="focus:ring-opacity-50 mt-1 block w-full rounded-md border-gray-300 bg-white p-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                >
                                    <option value="0" disabled selected class="hidden">Selecciona un cliente</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.client_id" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <Label for="quantity">Cantidad</Label>
                                <Input
                                    id="quantity"
                                    type="number"
                                    v-model="form.quantity"
                                    class="mt-1 block w-full"
                                    placeholder="Cantidad del producto"
                                    @blur="getPrice"
                                />
                                <InputError :message="form.errors.quantity" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <Label for="price">Precio</Label>
                                <Input
                                    id="price"
                                    type="number"
                                    v-model="form.price"
                                    class="mt-1 block w-full"
                                    placeholder="Precio del producto"
                                    readonly
                                />
                                <InputError :message="form.errors.price" class="mt-2" />
                            </div>
                            <div class="mt-4 flex items-center justify-end">
                                <button
                                    type="button"
                                    class="mr-2 rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600"
                                    @click="showModal = false; form.reset(); isEdit = false; saleId = null"
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
