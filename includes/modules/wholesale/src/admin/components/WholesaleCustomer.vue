<template>
    <div class="wholesale-customer-list">
        <h1 class="wp-heading-inline">{{ __( 'Wholesale Customers', 'dokan' ) }}</h1>
        <hr class="wp-header-end">

        <ul class="subsubsub">
            <li><router-link :to="{ name: 'WholesaleCustomer', query: { status: 'all' }}" active-class="current" exact v-html="sprintf( __( 'All <span class=\'count\'>(%s)</span>', 'dokan' ), counts.all )"></router-link> | </li>
            <li><router-link :to="{ name: 'WholesaleCustomer', query: { status: 'active' }}" active-class="current" exact v-html="sprintf( __( 'Active <span class=\'count\'>(%s)</span>', 'dokan' ), counts.active )"></router-link> | </li>
            <li><router-link :to="{ name: 'WholesaleCustomer', query: { status: 'deactive' }}" active-class="current" exact v-html="sprintf( __( 'Deactive <span class=\'count\'>(%s)</span>', 'dokan' ), counts.deactive )"></router-link></li>
        </ul>

        <search title="Search Customer" @searched="doSearch"></search>

        <list-table
            :columns="columns"
            :loading="loading"
            :rows="customers"
            :actions="actions"
            actionColumn="full_name"
            :show-cb="showCb"
            :total-items="totalItems"
            :bulk-actions="bulkActions"
            :total-pages="totalPages"
            :per-page="perPage"
            :current-page="currentPage"
            :action-column="actionColumn"

            not-found="No customers found."

            :sort-by="sortBy"
            :sort-order="sortOrder"
            @sort="sortCallback"

            @pagination="goToPage"
            @bulk:click="onBulkAction"
            @searched="doSearch"
        >
            <template slot="full_name" slot-scope="data">
                <img :src="data.row.avatar" :alt="getFullName( data.row )" width="50">
                <strong><a :href="editUrl(data.row.id)">{{ getFullName( data.row ) ? getFullName( data.row ) : __( '(no name)', 'dokan' ) }}</a></strong>
            </template>

            <template slot="email" slot-scope="data">
                <a :href="'mailto:' + data.row.email">{{ data.row.email }}</a>
            </template>

            <template slot="registered" slot-scope="data">
                {{ moment(data.row.registered).format('MMM D, YYYY') }}
            </template>

            <template slot="wholesale_status" slot-scope="data">
                <switches :enabled="data.row.wholesale_status == 'active'" :value="data.row.id" @input="onSwitch"></switches>
            </template>

            <template slot="row-actions" slot-scope="data">
                <span v-for="(action, index) in actions" :class="action.key">
                    <a v-if="action.key == 'edit'" :href="editUrl(data.row.id)">{{ action.label }}</a>
                    <a v-else-if="action.key == 'orders'" :href="ordersUrl(data.row.id)">{{ action.label }}</a>
                    <a v-else href="#" @click.prevent="onActionClick( action.key, data.row )">{{ action.label }}</a>
                    <template v-if="index !== (actions.length - 1)"> | </template>
                </span>
            </template>
        </list-table>
    </div>
</template>

<script>
let ListTable = dokan_get_lib('ListTable');
let Switches  = dokan_get_lib('Switches');
let Search    = dokan_get_lib('Search');

export default {

    name: 'WholesaleCustomer',

    components: {
        ListTable,
        Switches,
        Search
    },

    data () {
        return {
            showCb: true,
            counts: {
                deactive: 0,
                active: 0,
                all: 0
            },

            totalItems: 0,
            perPage: 20,
            totalPages: 1,
            loading: false,

            columns: {
                'full_name': {
                    label: this.__( 'Name', 'dokan' ),
                },
                'email': {
                    label: this.__( 'E-mail', 'dokan' )
                },
                'username': {
                    label: this.__( 'Username', 'dokan' )
                },
                'role': {
                    label: this.__( 'Roles', 'dokan' )
                },
                'registered': {
                    label: this.__( 'Registered', 'dokan' ),
                    sortable: true
                },
                'wholesale_status': {
                    label: this.__( 'Status', 'dokan' )
                }
            },
            actionColumn: 'full_name',
            actions: [
                {
                    key: 'edit',
                    label: this.__( 'Edit', 'dokan' )
                },
                {
                    key: 'orders',
                    label: this.__( 'Orders', 'dokan' )
                },
                {
                    key: 'delete',
                    label: this.__( 'Remove', 'dokan' )
                },
            ],
            bulkActions: [
                {
                    key: 'activate',
                    label: this.__( 'Active', 'dokan' )
                },
                {
                    key: 'deactivate',
                    label: this.__( 'Deactive', 'dokan' )
                }
            ],
            customers: []
        }
    },

    watch: {
        '$route.query.status'() {
            this.fetchWholesaleCustomers();
        },

        '$route.query.page'() {
            this.fetchWholesaleCustomers();
        },

        '$route.query.orderby'() {
            this.fetchWholesaleCustomers();
        },

        '$route.query.order'() {
            this.fetchWholesaleCustomers();
        },
    },

    computed: {
        currentStatus() {
            return this.$route.query.status || 'all';
        },

        currentPage() {
            let page = this.$route.query.page || 1;

            return parseInt( page );
        },

        sortBy() {
            return this.$route.query.orderby || 'registered';
        },

        sortOrder() {
            return this.$route.query.order || 'desc';
        }
    },

    created() {

        this.fetchWholesaleCustomers();
    },

    methods: {
        getFullName( row ) {
            return row.first_name + ' ' + row.last_name
        },

        doSearch(payload) {
            let self     = this;
            self.loading = true;

            dokan.api.get(`/wholesale/customers/?search=${payload}`, {
                page: this.currentPage,
                orderby: this.sortBy,
                order: this.sortOrder
            })
            .done((response, status, xhr) => {
                self.customers = response;
                self.loading = false;

                this.updatePagination(xhr);
            });
        },

        updatedCounts(xhr) {
            this.counts.deactive = parseInt( xhr.getResponseHeader('X-Status-Deactive') );
            this.counts.active   = parseInt( xhr.getResponseHeader('X-Status-Active') );
            this.counts.all      = parseInt( xhr.getResponseHeader('X-Status-All') );
        },

        updatePagination(xhr) {
            this.totalPages = parseInt( xhr.getResponseHeader('X-WP-TotalPages') );
            this.totalItems = parseInt( xhr.getResponseHeader('X-WP-Total') );
        },

        fetchWholesaleCustomers() {
            let self = this;
            self.loading = true;

            // dokan.api.get('/stores?per_page=' + this.perPage + '&page=' + this.currentPage + '&status=' + this.currentStatus)
            dokan.api.get('/wholesale/customers', {
                per_page: this.perPage,
                page: this.currentPage,
                status: this.currentStatus,
                orderby: this.sortBy,
                order: this.sortOrder
            })
            .done((response, status, xhr) => {
                // console.log(response, status, xhr);
                self.customers = response;
                self.loading = false;

                this.updatedCounts(xhr);
                this.updatePagination(xhr);
            });
        },

        onActionClick(action, row) {
            if ( 'delete' === action ) {
                if ( confirm('Are you sure to delete?') ) {
                    dokan.api.put('/wholesale/customer/' + row.id, {
                        status: 'delete'
                    })

                    .done(response => {
                        this.$notify({
                            title: this.__( 'Success!', 'dokan' ),
                            type: 'success',
                            text: this.__( 'Successfully removed from wholesale customer lists', 'dokan' ),
                        });

                        this.fetchWholesaleCustomers();
                    });
                }
            }
        },

        onSwitch( status, customer_id ) {

            let message = ( status === false ) ? this.__( 'The customer has been disabled for wholesale.', 'dokan' ) : this.__( 'Wholesale capability activate', 'dokan' );

            dokan.api.put('/wholesale/customer/' + customer_id, {
                status: ( status === false ) ? 'deactivate' : 'activate'
            })

            .done(response => {
                this.$notify({
                    title: this.__( 'Success!', 'dokan' ),
                    type: 'success',
                    text: message,
                });

                if (this.currentStatus !== 'all' ) {
                    this.fetchWholesaleCustomers();
                }
            });
        },

        moment(date) {
            return moment(date);
        },

        goToPage(page) {
            this.$router.push({
                name: 'WholesaleCustomer',
                query: {
                    status: this.currentStatus,
                    page: page
                }
            });
        },

        onBulkAction(action, items) {
            let jsonData = {};
            jsonData[action] = items;

            this.loading = true;

            dokan.api.put( '/wholesale/customers/batch', jsonData )
            .done(response => {
                this.bulkLocal = -1;
                this.checkedItems = [];
                this.loading = false;
                this.fetchWholesaleCustomers();
            });
        },

        sortCallback(column, order) {
            this.$router.push({
                name: 'WholesaleCustomer',
                query: {
                    status: this.currentStatus,
                    page: 1,
                    orderby: column,
                    order: order
                }
            });
        },

        ordersUrl(id) {
            return dokan.urls.adminRoot + 'edit.php?post_type=shop_order&_customer_user=' + id;
        },

        editUrl(id) {
            return dokan.urls.adminRoot + 'user-edit.php?user_id=' + id;
        },
    }
};
</script>

<style lang="less">
.wholesale-customer-list {

    .image {
        width: 10%;
    }

    .full_name {
        width: 25%;
    }

    .email {
        width: 20%;
    }

    td.full_name img {
        float: left;
        margin-right: 10px;
        margin-top: 1px;
        width: 24px;
        height: auto;
    }

    td.full_name strong {
        display: block;
        margin-bottom: .2em;
        font-size: 14px;
    }
}

@media only screen and (max-width: 600px) {
    .wholesale-customer-list {
        table {
            td.full_name, td.enabled {
                display: table-cell !important;
            }

            th:not(.check-column):not(.full_name):not(.enabled) {
                display: none;
            }

            td:not(.check-column):not(.full_name):not(.enabled) {
                display: none;
            }

            th.column, table td.column {
                width: auto;
            }

            td.manage-column.column-cb.check-column {
                padding-right: 15px;
            }

            th.column.enabled {
                width: 25% !important;
            }
        }
    }
}

@media only screen and (max-width:320px) {
    .wholesale-customer-list {
        table {
            .row-actions span {
                font-size: 11px;
            }
        }
    }
}
</style>
