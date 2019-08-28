import {
    PAGE_VIEWS,
    UNIQUE_PAGE_VIEWS,
    AVERAGE_TIME,
    BOUNCE_RATE
} from '../../../configs/page_views/buttonTypes.js';

export default {
    selectedPeriod: 'last_week',
    buttonData: {
        [PAGE_VIEWS]: {
            value: 0,
            isFetching: false
        },
        [UNIQUE_PAGE_VIEWS]: {
            value: 0,
            isFetching: false
        },
        [AVERAGE_TIME]: {
            value: 0,
            isFetching: false
        },
        [BOUNCE_RATE]: {
            value: 0,
            isFetching: false
        },
    },
    activeButton: PAGE_VIEWS,
    chartData: {
        items: [],
        isFetching: false
    },
};
