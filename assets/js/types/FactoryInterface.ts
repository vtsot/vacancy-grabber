export default interface FactoryInterface<T> {
    supports(data: Record<string, unknown>): boolean;

    create(data: Record<string, unknown>): T;
}
