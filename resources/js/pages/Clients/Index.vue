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
    name: 'ClientsIndex',
    components: {
        AppLayout,
    },
});

const props = defineProps({
    clients: Array,
});

const showModal = ref(false);
const dataList = ref([...props.clients]);
const isEdit = ref(false);
const clientId = ref(null);

const form = useForm({
    name: '',
    email: '',
    phone: '',
});

const createClient = () => {
    axios
        .post(route('customers.store'), form)
        .then((response) => {
            toast.success('Cliente creado con éxito', {
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
                toast.error('Error al crear el cliente');
            }
        });
};

const showEditModal = (product) => {
    isEdit.value = true;
    showModal.value = true;
    form.name = product.name;
    form.email = product.email;
    form.phone = product.phone;
    clientId.value = product.id;
};

const updateClient = () => {
    axios
        .put(route('customers.update', clientId.value), form)
        .then((response) => {
            if (response.status === 200) {
                toast.success('Cliente actualizado con éxito', {
                    theme: 'colored',
                });
                const index = dataList.value.findIndex((client) => client.id === clientId.value);
                dataList.value[index] = form.data();
                showModal.value = false;
                form.reset();
            } else {
                toast.error('Error al actualizar el cliente');
            }
        })
        .catch((error) => {
            if (error.response.status === 422) {
                form.setErrors(error.response.data.errors);
            } else {
                toast.error('Error al actualizar el cliente');
            }
        });
};

const deleteClient = (id) => {
    axios
        .delete(route('customers.destroy', { id }))
        .then((response) => {
            if (response.status === 200) {
                toast.success('Cliente eliminado con éxito', {
                    theme: 'colored',
                });
                const index = dataList.value.findIndex((client) => client.id === id);
                dataList.value.splice(index, 1);
            } else {
                toast.error('Error al eliminar el cliente');
            }
        })
        .catch((error) => {
            toast.error('Error al eliminar el cliente');
        });
};
</script>
<template>
    <Head title="Clients" />
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tus Clientes</h1>
                <button class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600" @click="showModal = true">Crear Cliente</button>
            </div>
            <div class="overflow-x-auto rounded-lg shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 border shadow-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Correo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Teléfono</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                        <tr v-for="client in dataList" :key="client.id" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">{{ client.name }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ client.email }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">{{ client.phone }}</td>
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
                                    @click="deleteClient(client.id)"
                                >
                                    <Trash class="h-4 w-4" />
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="dataList.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No hay clientes disponibles.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Modal :show="showModal" maxWidth="md">
                <template #default>
                    <div class="flex flex-col gap-4 p-6">
                        <h2 class="text-lg font-bold" v-if="isEdit">Editar Cliente</h2>
                        <h2 class="text-lg font-bold" v-else>Crear Cliente</h2>
                        <form @submit.prevent="isEdit ? updateClient() : createClient()">
                            <div class="mb-4">
                                <Label for="name">Nombre</Label>
                                <Input id="name" type="text" v-model="form.name" class="mt-1 block w-full" placeholder="Nombre del cliente" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <Label for="email">Correo</Label>
                                <Input id="email" type="email" v-model="form.email" class="mt-1 block w-full" placeholder="Correo del cliente" />
                                <InputError :message="form.errors.email" class="mt-2" />
                                <button
                                    type="button"
                                    class="mt-2 text-sm text-blue-500 hover:underline"
                                    @click="form.email = `${form.name.replace(/\s+/g, '').toLowerCase()}@example.com`"
                                >
                                    Usar correo genérico
                                </button>
                            </div>
                            <div class="mb-4">
                                <Label for="phone">Teléfono</Label>
                                <Input id="phone" type="text" v-model="form.phone" class="mt-1 block w-full" placeholder="Teléfono del cliente" />
                                <InputError :message="form.errors.phone" class="mt-2" />
                                <button type="button" class="mt-2 text-sm text-blue-500 hover:underline" @click="form.phone = '1234567890'">
                                    Usar teléfono genérico
                                </button>
                            </div>
                            <div class="mt-4 flex items-center justify-end">
                                <button
                                    type="button"
                                    class="mr-2 rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600"
                                    @click="showModal = false; form.reset(); isEdit = false; clientId = null"
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
