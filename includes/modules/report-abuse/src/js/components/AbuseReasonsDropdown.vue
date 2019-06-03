<template>
    <select v-model="selectedReason">
        <option value="">{{ noneText }}</option>
        <option
            v-for="reason in abuseReasons"
            :key="reason.id"
            v-text="reason.value"
        ></option>
    </select>
</template>

<script>
    export default {
        name: 'AbuseReasonsDropdown',

        props: {
            value: {
                type: String,
                required: true
            },

            placeholder: {
                type: String,
                required: false,
                default: ''
            }
        },

        data() {
            return {
                abuseReasons: [],
            };
        },

        computed: {
            selectedReason: {
                get() {
                    const reason = this.abuseReasons.filter((reason) => {
                        return this.value === reason.value;
                    });

                    if (reason.length) {
                        return reason[0].value;
                    }

                    return '';
                },

                set(reason) {
                    this.$emit('input', reason || '');
                }
            },

            noneText() {
                return this.placeholder || this.__('Select a reason', 'dokan');
            }
        },

        created() {
            this.fetchAbuseReasons();
        },

        methods: {
            fetchAbuseReasons() {
                const self = this;

                dokan.api.get('/abuse-reports/abuse-reasons').done((response) => {
                    self.abuseReasons = response;
                });
            }
        }
    };
</script>
