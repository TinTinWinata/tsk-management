import { ISchedule } from "@/Types/schedule";
import { IScheduleData } from "@/Types/schedule-data";
import axios, { AxiosError } from "axios";
import { useState } from "react";
import {
    DragDropContext,
    OnDragEndResponder,
    OnDragUpdateResponder,
} from "react-beautiful-dnd";
import { v4 as uuidv4 } from "uuid";
import ScheduleCard from "./ScheduleCard";

export interface IScheduleProps {
    datas: IScheduleData;
    token: string;
}

export interface IHoveredData {
    date: string;
    index: number;
    sourceDate: string;
}

export default function Schedule({
    datas: defaultSchedules,
    token,
}: IScheduleProps) {
    const [hoveredSchedule, setHoveredSchedule] = useState<IHoveredData>();
    const [datas, setDatas] = useState<IScheduleData>(defaultSchedules);
    const cloneDatas = () => {
        return JSON.parse(JSON.stringify(datas)) as IScheduleData;
    };

    const checkEmpty = (datas: IScheduleData) => {
        for (const data in datas) {
            if (datas.hasOwnProperty(data)) {
                datas[data].schedules = datas[data].schedules.filter(
                    (schedule) => schedule.title !== ""
                );
            }
        }
    };

    const saveDatas = async (newDatas: IScheduleData = datas) => {
        checkEmpty(newDatas);
        setDatas(newDatas);
        try {
            const response = await axios.post("/api/schedule/save", newDatas, {
                headers: { Authorization: "Bearer " + token },
            });
        } catch (err) {
            if (err instanceof AxiosError) {
                console.log("err : ", err.response.data.message);
            }
        }
    };

    const handleUnFocusText = (text: string, date: string, index: number) => {
        const newData = cloneDatas();
        newData[date].schedules[index].title = text;
        saveDatas(newData);
    };

    const onDragUpdate: OnDragUpdateResponder = async (e) => {
        if (e.destination) {
            const index = e.destination.index;
            const date = e.destination.droppableId;
            const data: IHoveredData = {
                sourceDate: e.source.droppableId,
                date,
                index,
            };
            setHoveredSchedule(data);
        }
    };

    const onDragEnd: OnDragEndResponder = async (e) => {
        setHoveredSchedule(undefined);
        if (!e.destination) {
            return;
        }

        if (
            e.destination.droppableId === e.source.droppableId &&
            e.destination.index === e.source.index
        ) {
            return;
        }

        const destDate = e.destination.droppableId;
        const destIndex = e.destination.index;

        const sourceIndex = e.source.index;
        const sourceDate = e.source.droppableId;

        const newData = cloneDatas();
        // Validate destination and source is not same
        if (destDate != sourceDate) {
            // Add new data to destination
            if (newData[destDate].schedules.length > 0) {
                newData[destDate].schedules.splice(
                    destIndex,
                    0,
                    newData[sourceDate].schedules[sourceIndex]
                );
            } else {
                newData[destDate].schedules.push(
                    newData[sourceDate].schedules[sourceIndex]
                );
            }

            // Remove old data from source
            newData[sourceDate].schedules.splice(sourceIndex, 1);
        } else {
            // Just swap the item
            const temp: ISchedule = newData[sourceDate].schedules[sourceIndex];
            newData[sourceDate].schedules.splice(sourceIndex, 1);
            newData[sourceDate].schedules.splice(destIndex, 0, temp);
        }

        saveDatas(newData);
    };

    const newSchedule = (): ISchedule => {
        return {
            date: new Date().toString(),
            id: uuidv4(),
            is_done: false,
            position: 0,
            title: "",
        };
    };

    const handleNewTask = (date: string) => {
        const newData = cloneDatas();
        newData[date].schedules = [newSchedule(), ...newData[date].schedules];
        setDatas(newData);
    };

    return (
        <DragDropContext onDragUpdate={onDragUpdate} onDragEnd={onDragEnd}>
            <div className="center min-h-screen w-full">
                <div className="w-full grid gap-x-5 gap-y-10 grid-cols-2">
                    {Object.keys(datas).map((val, index: number) => {
                        const data = datas[val];
                        return (
                            <ScheduleCard
                                onCheck={() => saveDatas()}
                                hovered={hoveredSchedule}
                                handleUnFocusText={handleUnFocusText}
                                handleNewTask={handleNewTask}
                                date={val}
                                index={index}
                                key={index}
                                formatted={data.formatted}
                                schedules={data.schedules}
                            />
                        );
                    })}
                </div>
            </div>
        </DragDropContext>
    );
}
