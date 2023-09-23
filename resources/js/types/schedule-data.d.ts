import { ISchedule } from "./schedule";

interface IScheduleDataDetail {
    formatted: string;
    schedules: ISchedule[];
}

export interface IScheduleData {
    [date: string]: IScheduleDataDetail;
}
