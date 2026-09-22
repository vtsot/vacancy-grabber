import RouteGenerator from "../module/RouteGenerator";
import {snakeCase} from "lodash";

export const pushUrlParams = (
    params: object,
    route: string | null = null
): void => {
    setUrlParams(params, route, true);
}

export const setUrlParams = (
    params: object,
    route: string | null = null,
    push = false
): void => {
    let url: URL | string | null = null;

    const stateParams = filterUrlParams(params),
        routeParams = sortUrlParams(stateParams);

    if (route) {
        url = RouteGenerator.generate(route, routeParams).replaceAll('%252B', '+');
    } else {
        url = new URL(window.location.href);
        url.search = '';
        Object.entries(routeParams)
            .forEach(([key, value]): void => {
                (url as URL).searchParams.append(key, value);
            });
        url.searchParams.sort();
    }

    if (push) {
        window.history.pushState(stateParams, '', url);
    } else {
        window.history.replaceState(stateParams, '', url);
    }
}

export const changeUrlParams = (
    params: object,
    route: string | null = null,
    push = false
): void => {
    const url = new URL(window.location.href);
    setUrlParams(
        Object.assign(
            window.history.state || Object.fromEntries(url.searchParams.entries()),
            params,
        ),
        route,
        push,
    );
}

export const filterUrlParams = (
    params: object
): object => {
    return Object.fromEntries(
        Object.entries(params)
            .filter(([, value]): boolean => value !== undefined && value !== null && value !== '')
            .map(([key, value]) => {
                return [snakeCase(key), value];
            })
    );
}

export const clearUrlParams = (
    route: string | null = null
): void => {
    setUrlParams({}, route);
};

export const sortUrlParams = (
    params: object
): object => {
    return Object.fromEntries(
        Object.keys(params)
            .sort()
            .map(key => [key, params[key]])
    );
}

export const getUrlParam = (name: string): string | null => {
    return new URL(window.location.href).searchParams.get(name);
}

export const getUrlParams = (): object => {
    return Object.fromEntries(new URL(window.location.href).searchParams.entries());
};
