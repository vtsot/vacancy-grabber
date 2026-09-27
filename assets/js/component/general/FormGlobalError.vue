<template>
    <v-alert type="error" variant="tonal" class="form-error">
        <div v-for="error in props.errors" :key="error">
            {{ error }}
        </div>
    </v-alert>
</template>

<script setup lang="ts">
    import {PropType} from "vue";

    const props = defineProps({
        errors: {
            type: Array as PropType<string[]>,
            required: true,
        },
    });
</script>
