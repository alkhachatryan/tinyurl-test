<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddProductToLastViewedProductsColumn implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $userId,
        protected int $productId,
        protected array $userLastViewedProducts
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userService = new UserService();
        $userService->addProductToUserLastViewedProducts($this->userId, $this->productId, $this->userLastViewedProducts);
    }
}
