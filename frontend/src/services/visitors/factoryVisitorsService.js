import {BOUNCE_RATE, NEW_VISITORS, TOTAL_VISITORS} from "../../configs/visitors/buttonTypes";
import totalVisitorsService from "./totalVisitorsService";
import newVisitorsService from "./newVisitorsService";
import bounceRateService from "./bounceRateService";

const create = (type) => {
    switch (type) {
        case TOTAL_VISITORS:
            return totalVisitorsService;
        case NEW_VISITORS:
            return newVisitorsService;
        case BOUNCE_RATE:
            return bounceRateService;
    }
};

const factoryVisitorsService = {
    create
};

export default factoryVisitorsService;