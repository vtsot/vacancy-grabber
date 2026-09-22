import RouteGenerator from "../module/RouteGenerator";
import {snakeCase} from "lodash";

export default class UrlParams {
    private readonly _url: URL;

    constructor(
        url?: string | null,
    ) {
        this._url = new URL(url ?? window.location.href);
    }

    get url(): URL {
        return this._url;
    }

    getParam(name: string): string | null {
        return this.url.searchParams.get(name);
    }

    get params(): object {
        return Object.fromEntries(this.url.searchParams);
    };

    pushUrlParams(params: object, route: string | null = null): void {
        this.setUrlParams(params, route, true);
    }

    setUrlParams(params: object, route: string | null = null, push = false): void {
        let url: URL | string | null = null;

        const stateParams = this.filterUrlParams(params),
            routeParams = this.sortUrlParams(stateParams);

        if (route) {
            url = RouteGenerator.generate(route, routeParams).replaceAll('%252B', '+');
        } else {
            url = this.url;
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
    };

    changeUrlParams(params: object, route: string | null = null, push = false): void {
        this.setUrlParams(
            Object.assign(
                window.history.state || Object.fromEntries(this.url.searchParams.entries()),
                params,
            ),
            route,
            push,
        );
    }

    clearUrlParams(route: string | null = null): void {
        this.setUrlParams({}, route);
    };

    filterUrlParams(params: object): object {
        return Object.fromEntries(
            Object.entries(params)
                .filter(([, value]): boolean => value !== undefined && value !== null && value !== '')
                .map(([key, value]) => {
                    return [snakeCase(key), value];
                })
        );
    }

    sortUrlParams(params: object): object {
        return Object.fromEntries(Object.keys(params).sort().map(key => [key, params[key]]));
    }
}

