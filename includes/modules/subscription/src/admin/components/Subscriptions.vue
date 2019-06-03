<template>
    <div class="subscription-list">
        <h1 class="wp-heading-inline">{{ __( 'Subscription User List', 'dokan') }}</h1>
        <hr class="wp-header-end">

        <ul class="subsubsub">
            <li><router-link to="" active-class="current" exact v-html="sprintf( __( 'Total Subscribed Vendors <span class=\'count\'>(%s)</span>', 'dokan' ), counts.all )"></router-link></li>
        </ul>

        <list-table
            :columns="columns"
            :loading="loading"
            :rows="vendors"
            :actions="actions"
            :show-cb="showCb"
            :total-items="totalItems"
            :bulk-actions="bulkActions"
            :total-pages="totalPages"
            :per-page="perPage"
            :current-page="currentPage"

            not-found="No vendors found."
            :sort-order="sortOrder"

            @pagination="goToPage"
            @bulk:click="onBulkAction"
        >
            <template slot="user_name" slot-scope="data">
                <strong><a :href="data.row.user_link">{{ data.row.user_name ? data.row.user_name : __( '(no name)', 'dokan' ) }}</a></strong>
            </template>

            <template slot="subscription_title" slot-scope="data">
                <strong><a :href="subscriptionUrl(data.row.subscription_id)">{{ data.row.subscription_title ? data.row.subscription_title : __( '(no name)', 'dokan' ) }}</a></strong>
            </template>

            <template slot="status" slot-scope="data">
                {{ data.row.status == 1 ? __( 'Active', 'dokan' ) : __( 'Inactive', 'dokan' ) }}
            </template>

            <template slot="action" slot-scope="data">
                <button class="button button-primary" @click="cancelSubscription(data.row.id)">{{ __( 'Cancel', 'dokan') }}</button>
            </template>
        </list-table>
    </div>
</template>

<script>
let ListTable = dokan_get_lib('ListTable');

export default {

    name: 'Subscriptions',

    components: {
        ListTable
    },

    data () {
        return {
            showCb: true,

            counts: {
                all: 0
            },

            totalItems: 0,
            perPage: 10,
            totalPages: 1,
            loading: false,

            columns: {
                'user_name': {
                    label: this.__( 'User Name', 'dokan' ),
                },
                'subscription_title': {
                    label: this.__( 'Subscription Pack', 'dokan' )
                },
                'start_date': {
                    label: this.__( 'Start Date', 'dokan' )
                },
                'end_date': {
                    label: this.__( 'End Date', 'dokan' ),
                },
                'status': {
                    label: this.__( 'Status', 'dokan' )
                },
                'action': {
                    label: this.__( 'Action', 'dokan' )
                }
            },
            actions: [],
            bulkActions: [
                {
                    key: 'cancel',
                    label: this.__( 'Cancel Subscription', 'dokan' )
                }
            ],
            vendors: []
        }
    },

    watch: {
        '$route.query.status'() {
            this.fetchSubscription();
        },

        '$route.query.page'() {
            this.fetchSubscription();
        },

        '$route.query.orderby'() {
            this.fetchSubscription();
        },

        '$route.query.order'() {
            this.fetchSubscription();
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
        this.fetchSubscription();
    },

    methods: {

        cancelSubscription(id) {
            if ( confirm( this.__( 'Are you sure to cancel the subscription?', 'dokan' ) ) ) {
                this.deleteSubscripton(id);
            }
        },

        updatedCounts(xhr) {
            this.counts.all = parseInt( xhr.getResponseHeader('X-WP-Total') );
        },

        updatePagination(xhr) {
            this.totalPages = parseInt( xhr.getResponseHeader('X-WP-TotalPages') );
            this.totalItems = parseInt( xhr.getResponseHeader('X-WP-Total') );
        },

        deleteSubscripton(id) {
            let self = this;

            self.loading = true;

            dokan.api.delete('/subscription/' + id )
            .done((response, status, xhr) => {
                location.reload();
            });
        },

        fetchSubscription() {
            let self = this;

            self.loading = true;

            // dokan.api.get('/subscription?number=' + this.perPage + '&paged=' + this.currentPage)
            dokan.api.get('/subscription', {
                per_page: self.perPage,
                paged: self.currentPage,
                order: this.sortOrder
            })
            .done((response, status, xhr) => {
                if ( response.code == 'no_subscription' ) {
                    return self.loading = false;
                }

                self.vendors = response;
                self.loading = false;

                this.updatedCounts(xhr);
                this.updatePagination(xhr);
            });
        },

        goToPage(page) {
            this.$router.push({
                name: 'Subscriptions',
                query: {
                    page: page
                }
            });
        },

        onBulkAction(action, items) {
            if ( ! confirm( this.__( 'Are you sure to cancel the subscription?', 'dokan' ) ) ) {
                return;
            }

            let jsonData = {};
            jsonData[action] = items;

            this.loading = true;

            dokan.api.delete('/subscription/batch', jsonData)
            .done(response => {
                location.reload();
            });
        },

        subscriptionUrl(id) {
            return dokan.urls.adminRoot + 'post.php?post=' + id + '&action=edit';
        },
    }
};
</script>

<style>
</style>
