import requestService from "@/services/requestService";
import config from "@/config";
import {buttonTransformer, chartTransformer, tableTransformer} from './transformers';
import _ from "lodash";

const resourceUrl = config.getApiUrl();

const fetchButtonValue = (startDate, endDate) => {
    return requestService.get(resourceUrl + '/button-visitors', {}, {
        'filter[startDate]': startDate,
        'filter[endDate]': endDate
    }).then(response => buttonTransformer(response.data))
        .catch(error => Promise.reject(
            new Error(
                _.get(
                    error,
                    'response.data.error.message',
                    'Something went wrong with getting total visitors'
                )
            )
        ));
};

const fetchChartValues = (startDate, endDate, interval) => {
    return requestService.get(resourceUrl + '/chart-total-visitors', {}, {
        'filter[startDate]': startDate,
        'filter[endDate]': endDate,
        'filter[period]': interval
    }).then(response => response.data.map(chartTransformer))
        .catch(error => Promise.reject(
            new Error(
                _.get(
                    error,
                    'response.data.error.message',
                    'Something went wrong with getting total visitors'
                )
            )
        ));
};

const fetchTableValues = (startDate, endDate, groupBy) => {
    return requestService.get(resourceUrl + '/visitors/by-table', {}, {
        'filter[start_date]': startDate,
        'filter[end_date]': endDate,
        'parameter': groupBy
    }).then(response => response.data.visitors.map(tableTransformer.bind(null, groupBy)))
        .catch(error => Promise.reject(
            new Error(
                _.get(
                    error,
                    'response.data.error.message',
                    'Something went wrong with getting total visitors'
                )
            )
        ));
};

const totalVisitorsService = {
    fetchButtonValue,
    fetchChartValues,
    fetchTableValues
};

export default totalVisitorsService;