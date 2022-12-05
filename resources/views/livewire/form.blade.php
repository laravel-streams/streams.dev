<div x-data="{{ $this->entry()->toJson() }}">
    <form wire:submit.prevent="submit" method="POST" class="p-8">
        
        <input x-model="email" type="text" name="email" class="border-black border mb-2">
        <br>
        <input type="text" name="password" class="border-black border mb-2">
        <br>
        <button class="btn">Submit</button>
    </form>
</div>
