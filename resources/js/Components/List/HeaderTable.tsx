interface IHeaderTableProps {
    name: string;
    icon: JSX.Element;
    className?: string;
}

export default function HeaderTable({
    name,
    icon,
    className,
}: IHeaderTableProps) {
    return (
        <td className={"border-t border-b p-2 " + className}>
            <div className="flex gap-2">
                <div className="center">{icon}</div>
                <div className="">{name}</div>
            </div>
        </td>
    );
}
