import moment from "moment";

export const formatDate = (date: string | Date) => {
    let dateObj: Date;
    if (typeof date === "string") {
        dateObj = new Date(date);
    } else {
        dateObj = date;
    }
    return moment(dateObj).format("MMMM Do YYYY");
};
