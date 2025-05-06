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
import { StreamBarcodeReader } from 'vue-barcode-reader';
import { toast } from 'vue3-toastify';

const auth = usePage().props.user;

defineComponent({
    name: 'ProductsIndex',
    components: {
        AppLayout,
    },
});

const props = defineProps({
    products: Array,
});

const showModal = ref(false);
const dataList = ref([...props.products]);
const isEdit = ref(false);
const productId = ref(null);
const manualBarcode = ref(false);
const showModalHistoryPrices = ref(false);
const historyPrices = ref([]);

const form = useForm({
    name: '',
    barcode: '',
});

const onBarcodeScanned = (value) => {
    form.barcode = value;
    // console.log('Barcode scanned:', value);
    manualBarcode.value = true;
};

const onLoaded = () => {
    // console.log('Barcode reader loaded');
};

const createProduct = () => {
    if (form.barcode === '') {
        toast.error('El código de barras es obligatorio', {
            theme: 'colored',
        });
        return;
    }
    if (form.name === '') {
        toast.error('El nombre es obligatorio', {
            theme: 'colored',
        });
        return;
    }

    axios
        .post(route('warehouses.products.store'), form.data())
        .then((response) => {
            toast.success('Producto creado con éxito', {
                theme: 'colored',
            });
            dataList.value.unshift(response.data.data);
            form.reset();
            showModal.value = false;
        })
        .catch((error) => {
            toast.error('Error al crear el producto', {
                theme: 'colored',
            });
            if (error.response && error.response.data.errors) {
                form.errors = error.response.data.errors;
            }
        });
};

const showEditModal = (product) => {
    form.name = product.name;
    form.barcode = product.barcode;
    isEdit.value = true;
    manualBarcode.value = true;
    showModal.value = true;
    productId.value = product.id;
};

const updateProduct = () => {
    if (form.barcode === '') {
        toast.error('El código de barras es obligatorio', {
            theme: 'colored',
        });
        return;
    }

    if (form.name === '') {
        toast.error('El nombre es obligatorio', {
            theme: 'colored',
        });
        return;
    }
    axios
        .put(route('warehouses.products.update', productId.value), form.data())
        .then((response) => {
            toast.success('Producto actualizado con éxito', {
                theme: 'colored',
            });
            const index = dataList.value.findIndex((product) => product.id === productId.value);
            if (index !== -1) {
                dataList.value[index] = form.data();
            }
            form.reset();
            showModal.value = false;
        })
        .catch((error) => {
            toast.error('Error al actualizar el producto', {
                theme: 'colored',
            });
            if (error.response && error.response.data.errors) {
                form.errors = error.response.data.errors;
            }
        });
};

const deleteProduct = (id) => {
    axios
        .delete(route('warehouses.products.destroy', { product: id }))
        .then(() => {
            toast.success('Producto eliminado con éxito', {
                theme: 'colored',
            });
            dataList.value = dataList.value.filter((product) => product.id !== id);
        })
        .catch(() => {
            toast.error('Error al eliminar el producto', {
                theme: 'colored',
            });
        });
};

const getHistoryPrices = (id) => {
    showModalHistoryPrices.value = true;
    axios.get(route('warehouses.products.show', { product: id }))
        .then((response) => {
            historyPrices.value = response.data.data;
        })
        .catch(() => {
            toast.error('Error al obtener el historial de precios', {
                theme: 'colored',
            });
        });
};
</script>
<template>
    <Head title="Productos" />
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tus Productos</h1>
                <button class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600" @click="showModal = true">Crear Producto</button>
            </div>
            <div class="overflow-x-auto rounded-lg shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 border shadow-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                Código de Barras
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                Precio de Compra
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                Precio de Venta
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Existencias</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                Ultima Actualización de Precio
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                        <tr v-for="product in dataList" :key="product.id" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                <button class="text-blue-500 hover:underline" @click="getHistoryPrices(product.id)">
                                    {{ product.name }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ product.barcode }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ product.purchase_price ? product.purchase_price : 'No disponible' }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ product.sale_price ? product.sale_price : 'No disponible' }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ product.stock ? product.stock : '0' }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ product.effective_date ? new Date(product.effective_date).toLocaleDateString() : 'No disponible' }}
                            </td>
                            <td class="flex space-x-2 px-6 py-4 whitespace-nowrap">
                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-blue-500 px-4 py-2 text-sm font-medium text-blue-600 shadow-sm transition hover:bg-blue-500 hover:text-white"
                                    @click="showEditModal(product)"
                                >
                                    <Pencil class="h-4 w-4" />
                                    Editar
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-red-500 px-4 py-2 text-sm font-medium text-red-600 shadow-sm transition hover:bg-red-500 hover:text-white"
                                    @click="deleteProduct(product.id)"
                                >
                                    <Trash class="h-4 w-4" />
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="dataList.length === 0">
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No hay productos disponibles.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Modal :show="showModalHistoryPrices" maxWidth="lg">
                <template #default>
                    <div class="flex flex-col gap-4 p-6">
                        <h2 class="text-lg font-bold">Historial de Precios</h2>
                        <table class="min-w-full divide-y divide-gray-200 border shadow-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Precio de Compra</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Precio de Venta</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                                <tr v-for="price in historyPrices" :key="price.id" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">{{ price.purchase_price }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">{{ price.sale_price }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">{{ new Date(price.effective_date).toLocaleDateString() }}</td>
                                </tr>
                                <tr v-if="!Array.isArray(historyPrices) || historyPrices.length === 0">
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No hay historial de precios disponible.</td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="mt-2 rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600" @click="showModalHistoryPrices = false">Cerrar</button>
                    </div>
                </template>
            </Modal>
            <Modal :show="showModal" maxWidth="md">
                <template #default>
                    <div class="flex flex-col gap-4 p-6">
                        <h2 class="text-lg font-bold" v-if="isEdit">Editar Producto</h2>
                        <h2 class="text-lg font-bold" v-else>Crear Producto</h2>
                        <form @submit.prevent="isEdit ? updateProduct() : createProduct()">
                            <div class="mb-4">
                                <Label for="name">Nombre</Label>
                                <Input id="name" type="text" v-model="form.name" class="mt-1 block w-full" placeholder="Nombre del producto" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                            <div class="mb-4 flex flex-col gap-2">
                                <Label for="barcode">Código de Barras</Label>
                                <Input
                                    v-if="manualBarcode"
                                    id="barcode"
                                    type="text"
                                    v-model="form.barcode"
                                    class="mt-1 block w-full"
                                    placeholder="Código de barras del producto"
                                />
                                <InputError :message="form.errors.barcode" class="mt-2" />

                                <StreamBarcodeReader @decode="onBarcodeScanned" @loaded="onLoaded" v-if="!manualBarcode" />
                                <button
                                    type="button"
                                    class="mt-2 inline-flex items-center gap-2 rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                    @click="manualBarcode = !manualBarcode"
                                >
                                    <span v-if="manualBarcode">Usar lector de código de barras</span>
                                    <span v-else>Ingresar manualmente</span>
                                </button>
                            </div>

                            <div class="mt-4 flex items-center justify-end">
                                <button
                                    type="button"
                                    class="mr-2 rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600"
                                    @click="showModal = false; form.reset(); isEdit = false; manualBarcode = false;"
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
