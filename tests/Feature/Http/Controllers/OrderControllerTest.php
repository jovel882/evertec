<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Models\Order;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderCreated;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        $this->seed();
    }

    /**
     *
     * @test
     * @return void
     */
    public function can_access_the_order_creation_form()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertViewIs("home")
            ->assertSeeText(config('config.product_name'))
            ->assertSeeText(config('config.product_price'));
    }

    /**
     *
     * @test
     * @return void
     */
    public function only_authenticated_users_can_submit_submit_the_order_creation_form()
    {
        $response = $this->post(
            route('orders.store')
        );

        $response->assertRedirect(route('login'));
    }
    
    /**
     *
     * @test
     * @return void
     */
    public function create_order_creation_form_and_receive_error()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->post(
            route('orders.store')
        );

        $response->assertRedirect(route('home'))
            ->assertSessionHasErrors(['quantity']);
    }

    /**
     *
     * @test
     * @return void
     */
    public function create_order_creation_form_successfully()
    {
        Notification::fake();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->post(
            route('orders.store'),
            [
                'quantity' => 2
            ]
        );

        $response->assertRedirect(route('orders.show', ["order" => 51]));
        $this->assertDatabaseHas(
            with(new Order)->getTable(),
            [
                'id' => 51,
                'quantity' => 2,
            ]
        );
        Notification::assertSentTo(
            $user,
            OrderCreated::class,
            function ($notification, $channels) {
                return array_search("mail", $channels)!==false && $notification->order->quantity === 2;
            }
        );
    }

    /**
     *
     * @test
     * @return void
     */
    public function only_authenticated_users_can_access_the_list_of_orders()
    {
        $response = $this->get(
            route('orders.index')
        );
        
        $response->assertRedirect(route('login'));
    }
    
    /**
     *
     * @test
     * @return void
     */
    public function only_users_with_permission_can_access_the_total_list_of_orders()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $orders = (new Order())->getAll(false);

        $response = $this->get(
            route('orders.index')
        );

        $response->assertViewIs("web.orders.list")
            ->assertViewHas('viewAny', false)
            ->assertViewHas('orders', $orders);
    }

    /**
     *
     * @test
     * @return void
     */
    public function access_with_a_user_with_permission_to_view_all_orders()
    {
        $user = factory(User::class)->create();
        $user->assignRole("SuperAdministrator");
        $this->actingAs($user);
        $orders = (new Order())->getAll(true);

        $response = $this->get(
            route('orders.index')
        );

        $response->assertViewIs("web.orders.list")
            ->assertViewHas('viewAny', true)
            ->assertViewHas('orders', $orders);
    }

    /**
     *
     * @test
     * @return void
     */
    public function only_authenticated_users_can_access_the_view_of_specific_order()
    {
        $response = $this->get(
            route('orders.show', ['order' => 1])
        );

        $response->assertRedirect(route('login'));
    }
    
    /**
     *
     * @test
     * @return void
     */
    public function only_users_with_permission_can_access_the_view_of_specific_of_orders()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->get(
            route('orders.show', ['order' => 1])
        );

        $response->assertForbidden();
    }

    /**
     *
     * @test
     * @return void
     */
    public function access_with_a_user_with_permission_can_access_the_view_of_any_specific_of_orders()
    {
        $user = factory(User::class)->create();
        $user->assignRole("SuperAdministrator");
        $this->actingAs($user);
        $order = (new Order())->getById(1, false);

        $response = $this->get(
            route('orders.show', ['order' => 1])
        );

        $response->assertViewIs("web.orders.view")
            ->assertViewHas('viewAny', true)
            ->assertViewHas('order', $order);
    }
}
