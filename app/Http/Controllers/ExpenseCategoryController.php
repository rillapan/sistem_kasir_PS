<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')->latest()->paginate(10);

        return view('expense-category.index', [
            'title' => 'Kategori Pengeluaran',
            'active' => 'expense-category',
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('expense-category.create', [
            'title' => 'Tambah Kategori Pengeluaran',
            'active' => 'expense-category'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255|unique:expense_categories,nama',
            'deskripsi' => 'nullable|string'
        ]);

        ExpenseCategory::create($validatedData);

        return redirect()->route('expense-category.index')->with('success', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = ExpenseCategory::findOrFail($id);

        return view('expense-category.edit', [
            'title' => 'Edit Kategori Pengeluaran',
            'active' => 'expense-category',
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = ExpenseCategory::findOrFail($id);

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255|unique:expense_categories,nama,' . $id,
            'deskripsi' => 'nullable|string'
        ]);

        $category->update($validatedData);

        return redirect()->route('expense-category.index')->with('success', 'Kategori pengeluaran berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        
        // Check if category has expenses
        if ($category->expenses()->count() > 0) {
            return redirect()->route('expense-category.index')
                ->with('gagal', 'Kategori tidak dapat dihapus karena masih memiliki data pengeluaran.');
        }

        $category->delete();

        return redirect()->route('expense-category.index')->with('success', 'Kategori pengeluaran berhasil dihapus.');
    }
}
