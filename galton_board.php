<?php declare(strict_types=1);

class GaltonBoard
{
    private int $numRows;

    private int $boardWidth;

    private int $boardHeight;

    private array $slotCounts;

    private GdImage $image;

    private int $pegRadius;

    private int $numBalls;

    public function __construct(
        int $numRows = 12,
        int $numBalls = 50000,
        int $boardWidth = 1000,
        int $boardHeight = 600
    ) {
        $this->numRows = $numRows;

        $this->numBalls = $numBalls;

        $this->boardWidth = $boardWidth;

        $this->boardHeight = $boardHeight;

        $this->slotCounts = array_fill(
            0, $this->boardWidth, 0
        );

        $this->pegRadius = 8;

        $this->image = imagecreatetruecolor(
            $this->boardWidth, $this->boardHeight
        );

        imagefill(
            $this->image, 0, 0, imagecolorallocate(
                $this->image, 102, 51, 153
            )
        );
    }

    public function simulate(): void
    {
        for ($ball = 0; $ball < $this->numBalls; $ball++) {
            $this->slotCounts[$this->calculateBin()]++;
        }
    }

    private function calculateBin(): int
    {
        $binIndex = $this->boardWidth / 2;

        for ($row = 0; $row < $this->boardHeight; $row++) {
            $binIndex += random_int(0, 1) == 0 ? -1 : 1;
        }

        return max(0, min($binIndex, $this->boardWidth - 1));
    }

    private function drawHistogram(): void
    {
        $maxFrequency = max($this->slotCounts);

        $barWidth = $this->boardWidth / count($this->slotCounts);

        for (
            $binIndex = 0; $binIndex < count($this->slotCounts); $binIndex++
        ) {
            $barHeight = (int) (
                (
                    ($this->slotCounts[$binIndex] / $maxFrequency)
                    * $this->boardHeight
                )
            );

            for ($y = 0; $y < $barHeight; $y++) {
                for ($x = 0; $x < $barWidth; $x++) {
                    $pixelX = $binIndex * $barWidth + $x;

                    $pixelY = $this->boardHeight - $y - 1;

                    $color = (
                        ($pixelX < $this->boardWidth / 2)
                            ? imagecolorallocate($this->image, 122, 122, 244)
                            : imagecolorallocate($this->image, 122, 244, 122)
                    );

                    imagesetpixel($this->image, $pixelX, $pixelY, $color);
                }
            }
        }
    }

    public function saveImage(string $filename = 'galton_board.png'): void
    {
        imagepng($this->image, $filename, 0);
    }

    public function generateImage(): GdImage
    {
        $this->drawHistogram();

        return $this->image;
    }

    public function __destruct()
    {
        imagedestroy($this->image);
    }
}

function generateGaltonBoard(): void
{
    $board = new GaltonBoard();

    $board->simulate();

    $board->generateImage();

    $board->saveImage();
}

generateGaltonBoard();

?>