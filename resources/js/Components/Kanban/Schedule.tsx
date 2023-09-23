import { ISchedule } from "@/types/schedule";
import { IScheduleData } from "@/types/schedule-data";
import axios from "axios";
import { useEffect, useState } from "react";
import { DragDropContext, OnDragEndResponder } from "react-beautiful-dnd";
import { v4 as uuidv4 } from "uuid";
import ScheduleCard from "./ScheduleCard";

export interface IScheduleProps {
    datas: IScheduleData;
    token: string;
}

export default function Schedule({
    datas: defaultSchedules,
    token,
}: IScheduleProps) {
    const [datas, setDatas] = useState<IScheduleData>(defaultSchedules);
    console.log(token);
    const cloneDatas = () => {
        return JSON.parse(JSON.stringify(datas)) as IScheduleData;
    };

    useEffect(() => {
        // Scroll to the center of the page when the component mounts
        window.scrollTo({
            top: window.innerHeight / 2,
            behavior: "smooth", // This adds smooth scrolling animation
        });
    }, []);

    const saveDatas = async (datas: IScheduleData) => {
        setDatas(datas);
        console.log(datas);
        try {
            const response = await axios.post("/api/schedule/save", datas, {
                headers: { Authorization: "Bearer " + token },
            });
        } catch (err) {
            console.log("err : ", err);
        }
    };

    const handleUnFocusText = (text: string, date: string, index: number) => {
        const newData = cloneDatas();
        newData[date].schedules[index].title = text;
        saveDatas(newData);
    };

    const onDragEnd: OnDragEndResponder = async (e) => {
        if (!e.destination) {
            return;
        }

        if (
            e.destination.droppableId === e.source.droppableId &&
            e.destination.index === e.source.index
        ) {
            return;
        }

        const descDate = e.destination.droppableId;
        const descIndex = e.destination.index;

        const sourceIndex = e.source.index;
        const sourceDate = e.source.droppableId;

        const newData = cloneDatas();

        // Add new data to destination
        if (newData[descDate].schedules.length > 0) {
            newData[descDate].schedules = newData[descDate].schedules.splice(
                descIndex,
                0,
                newData[sourceDate].schedules[sourceIndex]
            );
        } else {
            newData[descDate].schedules.push(
                newData[sourceDate].schedules[sourceIndex]
            );
        }

        // Remove old data from source
        newData[sourceDate].schedules.splice(sourceIndex, 1);

        saveDatas(newData);
    };

    const newSchedule = (): ISchedule => {
        return {
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
        <DragDropContext onDragEnd={onDragEnd}>
            <div className="center min-h-screen w-full">
                <div className="w-full grid gap-x-5 gap-y-10 grid-cols-2">
                    {Object.keys(datas).map((val, index: number) => {
                        const data = datas[val];
                        return (
                            <ScheduleCard
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
