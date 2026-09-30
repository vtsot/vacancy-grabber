import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import VacancyInfoFilters from "../../model/vacancy/VacancyInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import VacancyInfo from "../../model/vacancy/VacancyInfo";
import {computed, ref} from "vue";

// export const useVacancyPageStore = defineStore('vacancy-page-store', {
//     state: () => ({
//         loading: false,
//         items: [] as VacancyInfo[], // original loaded items
//         filters: new VacancyInfoFilters(),
//         errors: new ErrorValidation(),
//         paginator: new Paginator(),
//         availableSortBy: [] as string[] | [],
//         availableFilters: {
//             source: [] as {id: string, name: string} | [] | null,
//             company: [] as {id: string, name: string} | [] | null,
//             status: [] as {value: string, name: string} | [] | null,
//         },
//         appliedFilters: {} as object | null,
//     }),
//
//     getters: {
//         hasSortBy: (state) => (name: string): boolean => state.availableSortBy.includes(name),
//     },
//
//     actions: {
//
//         async create(data: object): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .post('api.vacancy.post', {}, data)
//                 .finally(() => this.loading = false);
//         },
//
//         async update(vacancyId: string, data: object) {
//             this.loading = true;
//             return HttpClient
//                 .put('api.vacancy.put', {id: vacancyId}, data)
//                 .finally(() => this.loading = false);
//         },
//
//         async loadDetails(vacancyId: string): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .get('api.vacancy.details', {id: vacancyId})
//                 .finally(() => this.loading = false);
//         },
//
//         async loadCollection() {
//             this.loading = true;
//             return HttpClient
//                 .get('api.vacancy.collection', this.filters.normalizeUrlParams())
//                 .finally(() => this.loading = false);
//         },
//
//         async patch(vacancyId: string, data: object) {
//             this.loading = true;
//             return HttpClient
//                 .patch('api.vacancy.patch', {id: vacancyId}, data)
//                 .finally(() => this.loading = false);
//         },
//
//         async delete(vacancyId: string): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .delete('api.vacancy.delete', {id: vacancyId})
//                 .finally(() => this.loading = false);
//         },
//
//         async loadVacancyNoteCollection(vacancyId: string, params: object | undefined = {}) {
//             this.loading = true;
//             return HttpClient
//                 .get('api.vacancy.note.collection', {...{id: vacancyId}, ...params ?? {}})
//                 .finally(() => this.loading = false);
//         },
//
//         async loadNoteDetails(vacancyId: string, noteId: string): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .get('api.vacancy.note.details', {id: vacancyId, noteId})
//                 .finally(() => this.loading = false);
//         },
//
//         async createNote(vacancyId: string, data: object): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .post('api.vacancy.note.post', {id: vacancyId}, data)
//                 .finally(() => this.loading = false);
//         },
//
//         async updateNote(vacancyId: string, noteId: string, data: object) {
//             this.loading = true;
//             return HttpClient
//                 .put('api.vacancy.note.put', {id: vacancyId, noteId}, data)
//                 .finally(() => this.loading = false);
//         },
//
//         async patchNote(vacancyId: string, noteId: string, data: object) {
//             this.loading = true;
//             return HttpClient
//                 .patch('api.vacancy.note.patch', {id: vacancyId, noteId}, data)
//                 .finally(() => this.loading = false);
//         },
//
//         async deleteNote(vacancyId: string, noteId: string): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .delete('api.vacancy.note.delete', {id: vacancyId, noteId})
//                 .finally(() => this.loading = false);
//         },
//     },
// });

export const useVacancyPageStore = defineStore('vacancy-page-store', () => {
    // State
    const isLoading = ref(false);
    const items = ref<VacancyInfo[]>([] as VacancyInfo[]); // original loaded items
    const filters = ref<VacancyInfoFilters>(new VacancyInfoFilters());
    const errors = ref<ErrorValidation>(new ErrorValidation());
    const paginator = ref<Paginator>(new Paginator());
    const availableSortBy = ref<string[]>([] as string[]);
    const availableFilters = ref({
        source: [] as { id: string, name: string } | [] | null,
        company: [] as { id: string, name: string } | [] | null,
        status: [] as { value: string, name: string } | [] | null,
    });
    const appliedFilters = ref < {} as object > ({} as object);

    // Getters
    const loading = computed(() => isLoading.value);
    const hasSortBy = computed(() => (name: string): boolean => availableSortBy.value.includes(name as string));

    // Actions

    const create = async (data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .post('api.vacancy.post', {}, data)
            .finally(() => isLoading.value = false);
    };

    const update = async (vacancyId: string, data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .put('api.vacancy.put', {id: vacancyId}, data)
            .finally(() => isLoading.value = false);
    };

    const loadDetails = async (vacancyId: string): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.vacancy.details', {id: vacancyId})
            .finally(() => isLoading.value = false);
    };

    const loadCollection = async (): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.vacancy.collection', filters.value.normalizeUrlParams())
            .finally(() => isLoading.value = false);
    };

    const patch = async (vacancyId: string, data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .patch('api.vacancy.patch', {id: vacancyId}, data)
            .finally(() => isLoading.value = false);
    };

    const deleteItem = async (vacancyId: string): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .delete('api.vacancy.delete', {id: vacancyId})
            .finally(() => isLoading.value = false);
    };

    const loadVacancyNoteCollection = async (vacancyId: string, params: object | undefined = {}): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.vacancy.note.collection', {...{id: vacancyId}, ...params ?? {}})
            .finally(() => isLoading.value = false);
    };

    const loadNoteDetails = async (vacancyId: string, noteId: string): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.vacancy.note.details', {id: vacancyId, noteId})
            .finally(() => isLoading.value = false);
    };

    const createNote = async (vacancyId: string, data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .post('api.vacancy.note.post', {id: vacancyId}, data)
            .finally(() => isLoading.value = false);
    };

    const updateNote = async (vacancyId: string, noteId: string, data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .put('api.vacancy.note.put', {id: vacancyId, noteId}, data)
            .finally(() => isLoading.value = false);
    };

    const patchNote = async (vacancyId: string, noteId: string, data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .patch('api.vacancy.note.patch', {id: vacancyId, noteId}, data)
            .finally(() => isLoading.value = false);
    };

    const deleteNote = async (vacancyId: string, noteId: string): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .delete('api.vacancy.note.delete', {id: vacancyId, noteId})
            .finally(() => isLoading.value = false);
    };

    return {
        loading,
        items,
        filters,
        errors,
        paginator,
        availableSortBy,
        availableFilters,
        appliedFilters,
        hasSortBy,
        // Actions
        create,
        update,
        loadDetails,
        loadCollection,
        patch,
        deleteItem,
        loadVacancyNoteCollection,
        loadNoteDetails,
        createNote,
        updateNote,
        patchNote,
        deleteNote,
    };
});
