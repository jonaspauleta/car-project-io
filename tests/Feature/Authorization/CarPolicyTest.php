<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;
use App\Policies\CarPolicy;

beforeEach(function () {
    $this->policy = new CarPolicy();
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('CarPolicy', function () {
    describe('viewAny', function () {
        it('allows authenticated users to view their cars', function () {
            expect($this->policy->viewAny($this->user))->toBeTrue();
        });
    });

    describe('view', function () {
        it('allows car owner to view their car', function () {
            $car = Car::factory()->create(['user_id' => $this->user->id]);

            expect($this->policy->view($this->user, $car))->toBeTrue();
        });

        it('denies non-owner from viewing car', function () {
            $car = Car::factory()->create(['user_id' => $this->otherUser->id]);

            expect($this->policy->view($this->user, $car))->toBeFalse();
        });
    });

    describe('create', function () {
        it('allows authenticated users to create cars', function () {
            expect($this->policy->create($this->user))->toBeTrue();
        });
    });

    describe('update', function () {
        it('allows car owner to update their car', function () {
            $car = Car::factory()->create(['user_id' => $this->user->id]);

            expect($this->policy->update($this->user, $car))->toBeTrue();
        });

        it('denies non-owner from updating car', function () {
            $car = Car::factory()->create(['user_id' => $this->otherUser->id]);

            expect($this->policy->update($this->user, $car))->toBeFalse();
        });
    });

    describe('delete', function () {
        it('allows car owner to delete their car', function () {
            $car = Car::factory()->create(['user_id' => $this->user->id]);

            expect($this->policy->delete($this->user, $car))->toBeTrue();
        });

        it('denies non-owner from deleting car', function () {
            $car = Car::factory()->create(['user_id' => $this->otherUser->id]);

            expect($this->policy->delete($this->user, $car))->toBeFalse();
        });
    });

    describe('restore', function () {
        it('allows car owner to restore their car', function () {
            $car = Car::factory()->create(['user_id' => $this->user->id]);

            expect($this->policy->restore($this->user, $car))->toBeTrue();
        });

        it('denies non-owner from restoring car', function () {
            $car = Car::factory()->create(['user_id' => $this->otherUser->id]);

            expect($this->policy->restore($this->user, $car))->toBeFalse();
        });
    });

    describe('forceDelete', function () {
        it('allows car owner to force delete their car', function () {
            $car = Car::factory()->create(['user_id' => $this->user->id]);

            expect($this->policy->forceDelete($this->user, $car))->toBeTrue();
        });

        it('denies non-owner from force deleting car', function () {
            $car = Car::factory()->create(['user_id' => $this->otherUser->id]);

            expect($this->policy->forceDelete($this->user, $car))->toBeFalse();
        });
    });
});
