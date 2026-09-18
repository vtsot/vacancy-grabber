<template>

    <v-form
        ref="form"
        validate-on="submit"
        @submit.prevent="$emit('filters:submit')"
        :loading="loading"
    >
        <v-row>
            <v-col cols="2">
                <v-text-field
                    v-model="sourceListPageStore.filters.startDay"
                    :loading="loading"
                    clearable
                    type="date"
                    label="Start Day"
                    placeholder="Start Day"
                    @update:model-value="$emit('filters:updated')"
                />
            </v-col>

            <v-col cols="2">
                <v-text-field
                    v-model="sourceListPageStore.filters.endDay"
                    :loading="loading"
                    clearable
                    type="date"
                    label="Start Day"
                    placeholder="Start Day"
                    @update:model-value="$emit('filters:updated')"
                />
            </v-col>

            <v-col cols="2">
                <v-select
                    v-model="sourceListPageStore.filters.active"
                    :loading="loading"
                    :items="ItemsActiveOptions"
                    clearable
                    label="Active"
                    placeholder="All"
                    @update:model-value="$emit('filters:updated')"
                />
            </v-col>
        </v-row>

        <v-divider class="my-2"/>

        <v-row>
            <v-col cols="6">
                <v-text-field
                    v-model="sourceListPageStore.filters.query"
                    :loading="loading"
                    clearable
                    label="Search"
                    placeholder="Search"
                    @update:model-value="$emit('filters:updated')"
                />
            </v-col>

            <v-col cols="4">
            </v-col>

            <v-col cols="1" class="pr-10">
                <v-btn
                    text="Search"
                    :loading="loading"
                    :disabled="loading"
                    color="secondary"
                    class="ml-5 text-right"
                    @click="$emit('filters:submit')"
                />
            </v-col>

            <v-col cols="1" class="pr-10">
                <v-btn
                    text="Create"
                    :loading="loading"
                    :disabled="loading"
                    color="primary"
                    class="ml-5 text-right"
                    @click="$emit('create:button')"
                />
            </v-col>

        </v-row>

    </v-form>

</template>

<script setup>
    import {computed} from "vue";
    import {useSourceListPageStore} from "../store";
    import {ItemsActiveOptions} from "../../../utils/ItemsActiveOptions";

    const sourceListPageStore = useSourceListPageStore();
    const emit = defineEmits(['filters:updated:', 'filters:submit', 'create:button'])
    const loading = computed(() => sourceListPageStore.loading);
</script>
