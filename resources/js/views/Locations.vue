<template>
    <div class="">
        <div class="row">
            <div class="col-8">
                <b-input-group
                    prepend="Search "
                >
                    <b-form-input type="search"></b-form-input>
                </b-input-group>
            </div>
            <div class="col">
                <b-input-group
                    prepend="Row "
                    class="pull-right"
                >
                    
                    <b-form-select :options="perPageoptions" v-model="perPage" @change="getItem(currentPage)"></b-form-select>
                    <b-button v-b-modal.add-modal size="sm">Add Item</b-button>
                </b-input-group>
            </div>
        </div>
        <!-- {{locations}} -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Description</th>
                    <th scope="col">Date Created</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="location in items" :key="location.id" 
                    @mouseover="onHoverSelectedItem(location)"
                    @mouseleave="hoverSelectedItem=false">
                    <th scope="row">{{location.id}} </th>
                    <td>{{location.name}} </td>
                    <td>{{location.description}} </td>
                    <td>
                        <b-button-group v-if="location === hoverSelectedItem">
                            <b-button v-b-modal.edit-modal @click="editItem(location.id)" size="sm">Edit</b-button>
                            <b-button @click="deleteItem(location.id)" size="sm">Delete</b-button>
                        </b-button-group>
                        <span v-else >{{location.created_at}}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <b-modal id="add-modal" title="Add Record" :hide-footer=true>
            <crud-form @completed="addItem" :modelName="this.$options.name" :fields="fields" :fieldsInit="{name: '', description: '', enabled: 1}"></crud-form>
        </b-modal>
        <b-modal id="edit-modal" title="Edit Record" :hide-footer=true>
            <crud-form @updated="updateItem" :id="selectedItem" :modelName="this.$options.name" :fields="fields" :fieldsInit="{name: '', description: '', enabled: ''}"></crud-form>
        </b-modal>
        <b-pagination
            v-model="currentPage"
            :total-rows="totalRows"
            :per-page="perPage"
            aria-controls="my-table"
            @input="getItem(currentPage)"
            align="center"
        >
            <template v-slot:first-text ><span class="text-info">First</span></template>
            <template v-slot:prev-text><span class="text-info">Prev</span></template>
            <template v-slot:next-text><span class="text-info">Next</span></template>
            <template v-slot:last-text><span class="text-info">Last</span></template>
        </b-pagination>

        <!-- <p class="mt-3 text-center">Showing Page: {{ currentPage }} of {{totalRows}}</p> -->
    </div>
</template>

<script>
import CrudForm from '../components/CrudForm';
export default {
    components: {
        CrudForm
    },
    name: 'locations',
    data() {
        return {
            fields: [
                {
                    name: 'name',
                    key: 'name',
                    placeholder: 'name',
                    type: 'text',
                    inputText: '',
                    size: 'sm',
                    autofocus: true
                },
                {
                    name: 'name',
                    key: 'description',
                    placeholder: 'description',
                    type: 'textarea',
                    inputText: '',
                    size: 'sm',
                    autofocus: false
                },
                // {
                //     name: 'name',
                //     key: 'location_id',
                //     placeholder: 'location',
                //     type: 'select',
                //     inputText: '',
                //     size: 'sm',
                //     options: [],
                //     autofocus: false,
                // },
                {
                    name: 'name',
                    key: 'enabled',
                    placeholder: 'Enabled',
                    type: 'checkbox',
                    inputText: '',
                    size: 'sm',
                    autofocus: false
                },
                // {
                //     name: 'radios-btn-default',
                //     key: 'sample',
                //     placeholder: 'sample',
                //     type: 'radio',
                //     inputText: '',
                //     size: 'sm',
                //     options: [
                //         { value: 'a', text: 'This is First option' },
                //         { value: 'b', text: 'This is Second option' },
                //         { value: 'c', text: 'Diabled', disabled: true }
                //     ],
                //     autofocus: false
                // },
                // {
                //     name: 'name',
                //     key: 'file',
                //     placeholder: 'file',
                //     type: 'file',
                //     inputText: '',
                //     size: 'sm',
                //     autofocus: false
                // },
                // {
                //     name: 'name',
                //     key: 'textarea',
                //     placeholder: 'textarea',
                //     type: 'textarea',
                //     inputText: '',
                //     size: 'sm',
                //     autofocus: false
                // }
            ],
            perPage: 10,
            perPageoptions: [
                { value: 10, text: '10' },
                { value: 20, text: '20' },
                { value: 50, text: '30' },
                { value: 100, text: '100' }
            ],
            currentPage: 1,
            totalRows: 0,
            showActionBtn: false,
            items: [],
            selectedItem: null,
            hoverSelectedItem: false
        };
    },
    mounted() {
        this.getItem(this.currentPage);
    },
    methods: {
        getItem (currentPage) {
            axios.get('/'+this.$options.name+'?page='+this.currentPage+'&items_per_page='+this.perPage).then(response => {
                this.perPage = response.data.per_page
                this.currentPage = response.data.current_page
                this.totalRows = response.data.total
                this.items = response.data.data
            });
        },
        editItem (itemId) {
            this.selectedItem = itemId;
        },
        deleteItem (itemId) {
            if(confirm("Do you really want to delete this record?")){
                axios.delete('/'+this.$options.name+'/'+itemId).then(response => {
                    this.$bvToast.toast("Record deleted", {
                        title: this.$options.name,
                        autoHideDelay: 5000
                    })
                    this.getItem(this.currentPage);
                });
            }
        },
        updateItem(item) {
            let foundIndex = this.items.findIndex(x => x.id === item.id);
            this.$set(this.items, foundIndex, item)
            this.$bvModal.hide('edit-modal')
            this.$bvToast.toast("Record updated", {
                title: this.$options.name,
                autoHideDelay: 5000
            })
        },
        addItem(item){
            this.items.unshift(item)
            this.$bvModal.hide('add-modal')
            this.$bvToast.toast("Record added", {
                title: this.$options.name,
                autoHideDelay: 5000
            })
        },
        onHoverSelectedItem (item) {
            this.hoverSelectedItem = item
        }
    }
};
</script>
