export default interface BaseCollectionTableOptions {
    page: number;
    itemsPerPage: number;
    sortBy: { key: string; order: "asc" | "desc" }[];
}
