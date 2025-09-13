<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;
use App\Policies\ModificationPolicy;

beforeEach(function () {
    $this->policy = new ModificationPolicy();
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->car = Car::factory()->create(['user_id' => $this->user->id]);
    $this->otherUserCar = Car::factory()->create(['user_id' => $this->otherUser->id]);
});

describe('ModificationPolicy', function () {
    describe('viewAny', function () {
        it('allows car owner to view modifications for their car', function () {
            expect($this->policy->viewAny($this->user, $this->car))->toBeTrue();
        });

        it('denies non-owner from viewing modifications for their car', function () {
            expect($this->policy->viewAny($this->user, $this->otherUserCar))->toBeFalse();
        });
    });

    describe('view', function () {
        it('allows car owner to view modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->car->id]);

            expect($this->policy->view($this->user, $modification))->toBeTrue();
        });

        it('denies non-owner from viewing modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->otherUserCar->id]);

            expect($this->policy->view($this->user, $modification))->toBeFalse();
        });
    });

    describe('create', function () {
        it('allows car owner to create modifications for their car', function () {
            expect($this->policy->create($this->user, $this->car))->toBeTrue();
        });

        it('denies non-owner from creating modifications for their car', function () {
            expect($this->policy->create($this->user, $this->otherUserCar))->toBeFalse();
        });
    });

    describe('update', function () {
        it('allows car owner to update modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->car->id]);

            expect($this->policy->update($this->user, $modification))->toBeTrue();
        });

        it('denies non-owner from updating modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->otherUserCar->id]);

            expect($this->policy->update($this->user, $modification))->toBeFalse();
        });
    });

    describe('delete', function () {
        it('allows car owner to delete modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->car->id]);

            expect($this->policy->delete($this->user, $modification))->toBeTrue();
        });

        it('denies non-owner from deleting modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->otherUserCar->id]);

            expect($this->policy->delete($this->user, $modification))->toBeFalse();
        });
    });

    describe('restore', function () {
        it('allows car owner to restore modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->car->id]);

            expect($this->policy->restore($this->user, $modification))->toBeTrue();
        });

        it('denies non-owner from restoring modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->otherUserCar->id]);

            expect($this->policy->restore($this->user, $modification))->toBeFalse();
        });
    });

    describe('forceDelete', function () {
        it('allows car owner to force delete modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->car->id]);

            expect($this->policy->forceDelete($this->user, $modification))->toBeTrue();
        });

        it('denies non-owner from force deleting modification for their car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->otherUserCar->id]);

            expect($this->policy->forceDelete($this->user, $modification))->toBeFalse();
        });
    });
});
