interface IInitialProps {
    initial: string;
    size?: "xl" | "lg";
}

export default function Initial({ initial, size = "lg" }: IInitialProps) {
    const character = initial.charAt(0).toUpperCase();
    const getSize = () => {
        if (size === "xl") return "w-9 h-9 text-md";
        else if (size === "lg") return "w-6 h-6 text-xs ";
    };
    return (
        <div
            className={`
            ${getSize()}
        ml-1 text-gray-500  font-bold center bg-initial rounded-md`}
        >
            {character}
        </div>
    );
}
