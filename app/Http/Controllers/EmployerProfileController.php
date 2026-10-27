<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmployerProfileRequest;
use App\Models\Employer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmployerProfileController extends Controller
{
    private function getEmployer(): Employer
    {
        $user = auth()->user();
        if (!$user->employer) {
            $user->employer()->create([
                'company_name' => $user->name . ' Studio',
                'industry' => 'Technology & Design',
            ]);
            $user->refresh();
        }
        return $user->employer;
    }

    public function edit(): View
    {
        $employer = $this->getEmployer();
        return view('employer.profile.edit', compact('employer'));
    }

    public function update(UpdateEmployerProfileRequest $request): RedirectResponse
    {
        $employer = $this->getEmployer();
        $data = $request->validated();

        if ($request->hasFile('logo_image')) {
            if ($employer->logo_image && Storage::disk('public')->exists($employer->logo_image)) {
                Storage::disk('public')->delete($employer->logo_image);
            }
            $data['logo_image'] = $request->file('logo_image')->store('logos', 'public');
        }

        $employer->update($data);

        return back()->with('success', 'Company profile, bio, and social links updated successfully.');
    }
}
