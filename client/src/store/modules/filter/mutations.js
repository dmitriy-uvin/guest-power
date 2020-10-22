import * as mutations from './types/mutations';

export default {
    [mutations.SET_FILTER_ITEM]: (state, filterItem) => {
        state.filterItems = {
            ...state.filterItems,
            [filterItem.id]: {
                ...state.filterItems[filterItem.id],
                name: filterItem.name,
                visible: filterItem.visible,
                type: filterItem.type,
                property: filterItem.property,
            }
        };
        if (filterItem.limit === 'from') {
            state.filterItems[filterItem.id].from = filterItem.from;
        }
        if (filterItem.limit === 'to') {
            state.filterItems[filterItem.id].to = filterItem.to;
        }
    },
    [mutations.DELETE_FILTER_ITEM]: (state, id) => {
        const result = {};
        Object.keys(state.filterItems).map(key => {
            if (key !== id) {
                result[key] = state.filterItems[key];
            }
        });
        state.filterItems = result;
    },
    [mutations.SHOW_FILTER_ITEMS]: state => {
        const res = {};
        Object.keys(state.filterItems).map(key => {
            res[key] = {
                ...state.filterItems[key],
                visible: true
            };
        });
        state.filterItems = res;
    },
    [mutations.CLEAR_FILTER_ITEMS]: state => {
        state.filterItems = {};
    }
}