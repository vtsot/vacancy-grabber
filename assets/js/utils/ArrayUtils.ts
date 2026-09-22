import _transform from 'lodash/transform';
import _isArray from 'lodash/isArray';
import _isObject from 'lodash/isObject';
import _camelCase from 'lodash/camelCase';
import _snakeCase from 'lodash/snakeCase';

const camelizeArrayKeys = obj => _transform(obj, (result: any[] | { [index: string]: any }, value, key: string | number, target) => {
    const camelKey = _isArray(target) ? key : _camelCase(key as string);

    result[camelKey] = _isObject(value) ? camelizeArrayKeys(value) : value;
});

const snakeArrayKeys = obj => _transform(obj, (result: any[] | { [index: string]: any }, value, key: string | number, target) => {
    const snakeKey = _isArray(target) ? key : _snakeCase(key as string);

    result[snakeKey] = _isObject(value) ? snakeArrayKeys(value) : value;
});

export {
    camelizeArrayKeys,
    snakeArrayKeys,
};
