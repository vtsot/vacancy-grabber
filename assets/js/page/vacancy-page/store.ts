import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import VacancyInfoFilters from "../../model/vacancy/VacancyInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";

export const useVacancyPageStore = defineStore('vacancy-page-store', {
    state: () => ({
        loading: false,
        filters: new VacancyInfoFilters(),
        errors: new ErrorValidation(),
        paginator: new Paginator(),
        //sortBy: [] as string[] | [] | any as any,
        availableSortBy: [] as string[] | [],
        availableFilters: {
            source: [] as {id: string, name: string} | [] | null,
            company: [] as {id: string, name: string} | [] | null,
            status: [] as {value: string, name: string} | [] | null,
        },
        appliedFilters: {} as object | null,
    }),

    getters: {
        hasSortBy: (state) => (name: string): boolean => state.availableSortBy.includes(name),
    },

    actions: {

        async create(data: object): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.vacancy.post', {}, data)
                .finally(() => this.loading = false);
        },

        async update(vacancyId: string, data: object) {
            this.loading = true;
            return HttpClient
                .put('api.vacancy.put', {id: vacancyId}, data)
                .finally(() => this.loading = false);
        },

        async loadDetails(vacancyId: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .get('api.vacancy.details', {id: vacancyId})
                .finally(() => this.loading = false);
        },

        async loadCollection() {
            this.loading = true;
            return HttpClient
                .get('api.vacancy.collection', this.filters.normalizeUrlParams())
                .finally(() => this.loading = false);
        },

        async patch(vacancyId: string, data: object) {
            this.loading = true;
            return HttpClient
                .patch('api.vacancy.patch', {id: vacancyId}, data)
                .finally(() => this.loading = false);
        },

        async delete(vacancyId: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .delete('api.vacancy.delete', {id: vacancyId})
                .finally(() => this.loading = false);
        },

        async loadVacancyNoteCollection(vacancyId: string, params: object | undefined = {}) {
            this.loading = true;
            return HttpClient
                .get('api.vacancy.note.collection', {...{id: vacancyId}, ...params ?? {}})
                .finally(() => this.loading = false);
        },

        async loadNoteDetails(vacancyId: string, noteId: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .get('api.vacancy.note.details', {id: vacancyId, noteId})
                .finally(() => this.loading = false);
        },

        async createNote(vacancyId: string, data: object): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.vacancy.note.post', {id: vacancyId}, data)
                .finally(() => this.loading = false);
        },

        async updateNote(vacancyId: string, noteId: string, data: object) {
            this.loading = true;
            return HttpClient
                .put('api.vacancy.note.put', {id: vacancyId, noteId}, data)
                .finally(() => this.loading = false);
        },

        async patchNote(vacancyId: string, noteId: string, data: object) {
            this.loading = true;
            return HttpClient
                .patch('api.vacancy.note.patch', {id: vacancyId, noteId}, data)
                .finally(() => this.loading = false);
        },

        async deleteNote(vacancyId: string, noteId: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .delete('api.vacancy.note.delete', {id: vacancyId, noteId})
                .finally(() => this.loading = false);
        },
    },
});
