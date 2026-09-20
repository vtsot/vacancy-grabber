<template>
    <v-alert type="error" :icon="mdiCloseCircle" border class="mb-5" variant="tonal">
        <div v-for="error in props.errors" :key="error">
            {{ error }}
        </div>
    </v-alert>
</template>

<script setup lang="ts">
    import { mdiCloseCircle } from '@mdi/js';
    import {PropType} from "vue";

    const props = defineProps({
        errors: {
            type: Array as PropType<string[]>,
            required: true,
        },
    });
</script>
