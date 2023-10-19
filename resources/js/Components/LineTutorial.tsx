import { IUser } from "@/Types/page";
import axios from "axios";
import toast from "react-hot-toast";
import PrimaryButton from "./PrimaryButton";

interface ILineTutorial {
    user: IUser;
}

export default function LineTutorial({ user }: ILineTutorial) {
    const handleClickTest = async () => {
        const response = await axios.get("/api/line/test", {
            headers: {
                Authorization: "Bearer " + user.token,
            },
        });
        if (response.status === 200) {
            toast(response.data, {
                duration: 4000,
                icon: "🫡",
            });
        } else {
            toast(response.data, {
                duration: 4000,
                icon: "😢",
            });
        }
    };
    return (
        <div className=" py-3">
            <div className="center">
                <img className="w-52 h-52" src="/line_qr.png" />
            </div>
            <div className="mx-5 flex flex-col">
                <div className="text-gray-600">Line QR link</div>
                <div className="mb-2 mt-1 text-xs text-gray-400">
                    <div className="">1. Open line</div>
                    <div className="">2. Add friend line bot with QR code</div>
                    <div className="">
                        3. Login with : '/login [username] [password]'
                    </div>
                </div>
                <PrimaryButton onClick={handleClickTest} className=" center">
                    Test Hook
                </PrimaryButton>
            </div>
        </div>
    );
}
