export function swapItems<T>(arr: T[], index1: number, index2: number): T[] {
    if (
        index1 < 0 ||
        index1 >= arr.length ||
        index2 < 0 ||
        index2 >= arr.length
    ) {
        return arr;
    }

    const temp: T = arr[index1];
    arr[index1] = arr[index2];
    arr[index2] = temp;

    return arr;
}
