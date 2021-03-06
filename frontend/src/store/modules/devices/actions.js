import {
    CHANGE_SELECTED_PERIOD,
    FETCH_WIDGET_INFO
} from "./types/actions";
import {
    SET_SELECTED_PERIOD,
    SET_WIDGET_DATA,
    SET_DATA_FETCHING,
    RESET_DATA_FETCHING
} from "./types/mutations";

import { getTimeByPeriod } from "@/services/periodService";
import { fetchDevicesAndSystemsData } from "@/api/widgets/devicesAndSystemsService";

export default {
    [CHANGE_SELECTED_PERIOD]: (context, period) => {
        if (context.state.selectedPeriod === period.value) {
            return;
        }
        context.commit(SET_SELECTED_PERIOD, period);
        context.dispatch(FETCH_WIDGET_INFO);
    },

    [FETCH_WIDGET_INFO]: (context, withOverlay=true) => {
        const period = getTimeByPeriod(context.state.selectedPeriod);
        const startDate = period.startDate;
        const endDate = period.endDate;

        if (withOverlay) {
            context.commit(SET_DATA_FETCHING);
        }
        return fetchDevicesAndSystemsData(startDate.unix(), endDate.unix())
            .then(response => {
                context.commit(SET_WIDGET_DATA, response);
            })
            .finally(() => {
                context.commit(RESET_DATA_FETCHING);
            });
    }
};