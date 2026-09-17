<template>

    <v-form
        ref="form"
        validate-on="submit"
        @submit.prevent="onSubmit"
        :loading="loading"
    >
        <v-row>
            <v-col cols="3">
                <v-text-field
                    v-model="filters.startDay"
                    :loading="loading"
                    clearable
                    type="date"
                    label="Start Day"
                    placeholder="Start Day"
                    @update:model-value="$emit('update:filters', filters)"
                />
            </v-col>

            <v-col cols="3">
                <v-text-field
                    v-model="filters.endDay"
                    :loading="loading"
                    clearable
                    type="date"
                    label="Start Day"
                    placeholder="Start Day"
                    @update:model-value="$emit('update:filters', filters)"
                />
            </v-col>
        </v-row>
        <v-divider class="my-2"/>

        <v-row>
            <v-col cols="6">
                <v-text-field
                    v-model="filters.query"
                    :loading="loading"
                    clearable
                    label="Search"
                    placeholder="Search"
                    @update:model-value="$emit('update:filters', filters)"
                />
            </v-col>

            <v-col cols="3">
                <v-select
                    v-model="filters.active"
                    :loading="loading"
                    :items="itemsActiveOptions"
                    clearable
                    label="Active"
                    placeholder="All"
                    @update:model-value="$emit('update:filters', filters)"
                />
            </v-col>

            <v-col cols="3">
                <v-btn
                    text="Search"
                    :loading="loading"
                    :disabled="loading"
                    color="secondary"
                    @click="$emit('submit', filters)"
                />

            </v-col>

        </v-row>

    </v-form>

</template>

<script>
    import {defineComponent} from "vue";
    import {mapStores, mapState, mapActions} from "pinia";
    import {useSourceListPageStore} from '../store';
    import SourceInfoFilters from "../../../model/source/SourceInfoFilters";
    import {itemsActiveOptions} from "../../../utils/ItemsActiveOptions";


    export default defineComponent({

        emits: ['update:filters', 'submit'],

        computed: {
            ...mapState(useSourceListPageStore, ['loading']),
        },

        setup() {
            return {
                itemsActiveOptions,
            };
        },

        data() {
            return {
                filters: new SourceInfoFilters(),
            };
        },


    });
</script>
