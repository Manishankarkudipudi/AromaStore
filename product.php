<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Product Page</title>
    <link rel="stylesheet" href="<?php echo $server; ?>css/test.css">
    
</head>

<body>
    <div class="col-lg-3 col-md-4 col-sm-6 my-2">
        <article class="card m-auto product">
            <figure class="m-0">
                <img class="card-img-top" src="<?= $server; ?>img/products/<?= $r['image']; ?>" alt="Product Image of <?= htmlspecialchars($r['title']); ?>">
            </figure>
            <div class="card-body">
                <h4 class="card-title"><?= htmlspecialchars($r['title']); ?></h4>
                <p class="card-text"><?= htmlspecialchars($r['brand']); ?></p>
                <p class="price">$<?= htmlspecialchars($r['price']); ?></p>

                <!-- Begin Add to Cart Form -->
                <form class="add-to-cart-form" method="post" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?= $r['id']; ?>">
                    <input type="hidden" name="title" value="<?= htmlspecialchars($r['title']); ?>">
                    <input type="hidden" name="price" value="<?= htmlspecialchars($r['price']); ?>">

                    <!-- Increment/Decrement Section and Add to Cart Combined -->
                    <div class="quantity-and-cart" style="display: flex; align-items: center; gap: 10px;">
    <button type="button" onclick="modifyQuantity(-1, event)" style="background-color: #ff0000; border: 1px solid #bb0000; padding: 3px 7px; cursor: pointer;">-</button>
    <input type="number" name="quantity" class="quantity" value="1" min="1" style="width: 30px; border: 1px solid #bbb; text-align: center; margin: 0 10px;">
    <button type="button" onclick="modifyQuantity(1, event)" style="background-color: #00ff00; border: 1px solid #00bb00; padding: 3px 7px; cursor: pointer;">+</button>
    <button type="submit" class="btn buy-button">Add To Cart</button>
</div>

                </form>
                <!-- End Add to Cart Form -->
            </div>
        </article>
    </div>

</body>

</html>
