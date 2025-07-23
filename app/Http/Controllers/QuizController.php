<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File; 
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;


class QuizController extends Controller
{
    //
    public function manageExam(Request $request)
    {
        if ($request->ajax()) {
            $data = Quiz::select("*")
                ->from("quizzes")
                ->orderBy('id', 'asc')
                ->get();
    
            $data = $data->map(function ($item, $key) {
                $item->row_number = $key + 1; // Assign the row number starting from 1
                return $item;
            });
    
            return DataTables::of($data)
                ->addColumn('action', function ($row) {})
                ->rawColumns(['action'])
                ->make(true);
        }
    
        return view('/pages/admin/manageExam');
    }
    
    
    public function storeExam(Request $request)
    {
        $this->validate($request,[
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
            'tipe_tes' => 'required',
            'duration' => 'required|integer',
            'limit' => 'required|integer',
            
        ]);

        $quiz = new Quiz;

        $quiz->name = $request->input('title');
        $quiz->description = $request->input('desc');
        $quiz->type = $request->input('tipe_tes');
        $quiz->time = $request->input('duration');
        $quiz->quiz_limit = $request->input('limit');
        
        $quiz->save();
        return redirect('/admin/manage-exam')->with('success', 'Data psikotes berhasil ditambahkan');

        
    }


    public function manageExamEdit(Request $request)
    {     
        $quizId = $request->input('id');
    
        // Retrieve the quiz data based on the provided ID
        $quiz = Quiz::find($quizId);
    
        // Check if the quiz exists
        if (!$quiz) {
            abort(404);
        }
    
        // Update the quiz data with the new values
        $quiz->name = $request->input('name');
        $quiz->description = $request->input('description');
        $quiz->type = $request->input('type');
        $quiz->time = $request->input('time');
        $quiz->quiz_limit = $request->input('quiz_limit');
        $quiz->save();
    
        // You can choose how to handle the response after updating the quiz
        // For example, you can redirect to a different page or return a JSON response
    
        return redirect('/admin/manage-exam');
    
        // Return a JSON response
        // return response()->json(['message' => 'Quiz updated successfully'], 200);
    }
    

    public function manageExamDelete(Request $request)
    {
        $quizId = $request->input('id');
    
        $quiz = Quiz::find($quizId);
    
        if (!$quiz) {
            abort(404);
        }
    
        $quiz->delete();
    
        return redirect('/admin/manage-exam')->with('success', 'Psikotes berhasil dihapus.');
    }
    


    public function manageExamQuestion()
    {
        $quizzes = Quiz::oldest()->get();
        $editableContent = file_get_contents(public_path('edited_content.txt'));
    
        foreach ($quizzes as $quiz) {
            if ($quiz->type === 'Pilihan-Ganda') {
                $quiz->route = route('detail.exam', ['quiz' => $quiz->id]);
                $quiz->exampleRoute = route('example.exam', ['quiz' => $quiz->id]);
            } elseif ($quiz->type === 'Essay') {
                $quiz->route = route('essay.exam', ['quiz' => $quiz->id]);
                $quiz->exampleRoute = route('essay.example', ['quiz' => $quiz->id]);
            }
            
            $quiz->questionCount = Question::where('quiz_id', $quiz->id)
                ->where('is_example', 0)
                ->count();
        }
    
        return view('/pages/admin/manageExamQuestion', ['quizzes' => $quizzes, 'editableContent' => $editableContent]);
    }
    
    
    public function manageExamQuestionUpdate(Request $request)
    {
        $content = $request->input('content');
        file_put_contents(public_path('edited_content.txt'), $content);

        return redirect()->route('manage.examQuestion')->with('success', 'Konten berhasil diperbarui.');
    }


    public function detailExam(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = Question::select("*")
                ->from("questions")
                ->where('quiz_id', $id)
                ->where('is_example', 0)
                ->orderBy('id', 'asc')
                ->get();
    
            $data = $data->map(function ($question, $index) {
                $question->row_number = $index + 1;
                return $question;
            });
    
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    // Add your action buttons here
                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $quiz = Quiz::find($id);
        return view('/pages/admin/detailExamQuestion', compact('quiz'));
    }


    public function essayExam(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = Question::select("*")
                ->from("questions")
                ->where('quiz_id', $id)
                ->where('is_example', 0)
                ->orderBy('id', 'asc')
                ->get();
    
            $data = $data->map(function ($question, $index) {
                $question->row_number = $index + 1;
                return $question;
            });
    
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    // Add your action buttons here
                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $quiz = Quiz::find($id);
        return view('/pages/admin/essayExamQuestion', compact('quiz'));
    }


    public function storeQuestion(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
    
        $this->validate($request, [
            'input_type' => 'required|in:text,image',
            'image' => $request->input('input_type') === 'image' ? 'nullable|mimes:jpeg,png,jpg,gif|max:2048' : '',
            'question' => $request->input('input_type') === 'text' ? 'nullable|string|max:255' : '',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'radio_option' => 'required|in:A,B,C,D,E'
        ]);
    
        $question = new Question;
    
        if ($request->input('input_type') === 'image') {
            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');
    
                if ($uploadedFile->isValid()) {
                    $fileName = time() . '.' . $uploadedFile->getClientOriginalExtension();
                    $uploadedFile->move(public_path('files/question'), $fileName);
                    $question->image = $fileName;
                } else {
                    return redirect()->back()->with('error', 'File tidak valid. Silakan unggah file gambar yang valid.');
                }
            } else {
                return redirect()->back()->with('error', 'Input gambar diperlukan untuk pertanyaan gambar.');
            }
        } elseif ($request->input('input_type') === 'text') {
            $question->question = $request->input('question');
        } else {
            return redirect()->back()->with('error', 'Jenis input yang dipilih tidak valid.');
        }
        
        $question->option_a = $request->input('option_a');
        $question->option_b = $request->input('option_b');
        $question->option_c = $request->input('option_c');
        $question->option_d = $request->input('option_d');
        $question->option_e = $request->input('option_e');
        $question->is_correct = $request->input('radio_option');
        
        $isExample = $request->input('contoh', 0);
        $question->is_example = $isExample;

        $question->example_explanation = $request->input('penjelasan');
            
        $quiz->questions()->save($question);
    
        return back()->with('success', 'Pertanyaan berhasil ditambahkan');
    }

    
    public function storeEssay(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
    
        $this->validate($request, [
            'input_type' => 'required|in:text,image',
            'image' => $request->input('input_type') === 'image' ? 'nullable|mimes:jpeg,png,jpg,gif|max:2048' : '',
            'question' => $request->input('input_type') === 'text' ? 'nullable|string|max:255' : '',
            'correct' => 'required|string|max:255'
        ]);
    
        $question = new Question;
    
        if ($request->input('input_type') === 'image') {
            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');
    
                if ($uploadedFile->isValid()) {
                    $fileName = time() . '.' . $uploadedFile->getClientOriginalExtension();
                    $uploadedFile->move(public_path('files/question'), $fileName);
                    $question->image = $fileName;
                } else {
                    return redirect()->back()->with('error', 'File tidak valid. Silakan unggah file gambar yang valid.');
                }
            } else {
                return redirect()->back()->with('error', 'Input gambar diperlukan untuk pertanyaan gambar.');
            }
        } elseif ($request->input('input_type') === 'text') {
            $question->question = $request->input('question');
        } else {
            return redirect()->back()->with('error', 'Jenis input yang dipilih tidak valid.');
        }
    
        $question->is_correct = $request->input('correct');

        $isExample = $request->input('contoh', 0);
        $question->is_example = $isExample;

        $question->example_explanation = $request->input('penjelasan');

        $quiz->questions()->save($question);
    
        return back()->with('success', 'Pertanyaan berhasil ditambahkan');
    }
    

    public function storeQuestionImage(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
    
        // Validate the question image separately from the options
        $this->validate($request, [
            'input_type_question' => 'required|in:upload,reuse,text',
            'image_upload_question' => $request->input('input_type_question') === 'upload' ? 'nullable|mimes:jpeg,png,jpg,gif|max:2048' : '',
            'image_use_question' => $request->input('input_type_question') === 'reuse' ? 'nullable|string|max:255' : '',
            'question1' => $request->input('input_type_question') === 'text' ? 'nullable|string|max:255' : '',
        ]);
    
        $question = new Question;
    
        if ($request->input('input_type_question') === 'upload' && $request->hasFile('image_upload_question')) {
            // If it's an upload and the file is provided, handle the image upload for the question
            $image = $request->file('image_upload_question');
            $imageNameQuestion = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('files/question'), $imageNameQuestion);
            $question->image = $imageNameQuestion; // Save the image name in the 'image' field of the question
        } elseif ($request->input('input_type_question') === 'reuse' && $request->filled('image_use_question')) {
            // If it's a reuse and the image_use_question field is provided, handle the image reuse for the question
            $selectedImageFilename = $request->input('image_use_question');
            $selectedImagePath = storage_path('app/public/images/' . $selectedImageFilename);
    
            if (File::exists($selectedImagePath)) {
                $imageNameQuestion = time() . '_' . $selectedImageFilename;
                File::copy($selectedImagePath, public_path('files/question/' . $imageNameQuestion));
                $question->image = $imageNameQuestion;
            } else {
                // Handle the case when the selected image does not exist
                return redirect()->back()->with('error', 'Gambar yang dipilih untuk pertanyaan tidak ada.');
            }
        } elseif ($request->input('input_type_question') === 'text') {
            // If it's a text question, save the question text in the 'question' field of the question
            $question->question = $request->input('question1');
        }

        // Process options A, B, C, D
        $optionLetters = ['A', 'B', 'C', 'D'];
        foreach ($optionLetters as $optionLetter) {
            $inputTypeField = 'input_type_option' . $optionLetter;
            $imageUploadField = 'image_upload_option' . $optionLetter;
            $imageUseField = 'image_use_option' . $optionLetter;
            $optionField = 'option_' . strtolower($optionLetter);

            // Validate each option image separately
            $this->validate($request, [
                $inputTypeField => 'required|in:upload,reuse',
                $imageUploadField => $request->input($inputTypeField) === 'upload' ? 'mimes:jpeg,png,jpg,gif|max:2048' : '',
                $imageUseField => $request->input($inputTypeField) === 'reuse' ? 'string|max:255' : '',
            ]);

            // Process each option image
            if ($request->input($inputTypeField) === 'upload' && $request->hasFile($imageUploadField)) {
                // If it's an upload and the file is provided, handle the image upload for the question
                $imageOption = $request->file($imageUploadField);
                $imageNameOption = time() . '_' . $imageOption->getClientOriginalName();
                $imageOption->move(public_path('files/question'), $imageNameOption);
                $question->$optionField = $imageNameOption; // Save the image name in the 'image' field of the question
            } elseif ($request->input($inputTypeField) === 'reuse' && $request->filled($imageUseField)) {
                // If it's a reuse and the $imageUseField field is provided, handle the image reuse for the question
                $selectedOptionFilename = $request->input($imageUseField);
                $selectedOptionPath = storage_path('app/public/images/' . $selectedOptionFilename);
    
                if (File::exists($selectedOptionPath)) {
                    $OptionNameQuestion = time() . '_' . $selectedOptionFilename;
                    File::copy($selectedOptionPath, public_path('files/question/' . $OptionNameQuestion));
                    $question->$optionField = $OptionNameQuestion;
                } else {
                    // Handle the case when the selected Option does not exist
                    return redirect()->back()->with('error', 'Opsi yang dipilih $optionLetters untuk pertanyaan tersebut tidak ada.');
                }
            }
        }

        $this->validate($request, [
            'input_type_optionE' => 'required|in:upload,reuse', 
            'image_upload_optionE' => $request->input('input_type_optionE') === 'upload' ? 'mimes:jpeg,png,jpg,gif|max:2048' : '',
            'image_use_optionE' => $request->input('input_type_optionE') === 'reuse' ? 'nullable|string|max:255' : '',
        ]);
                     
        if ($request->input('input_type_optionE') === 'upload' && $request->hasFile('image_upload_optionE')) {
            // If it's an upload and the file is provided, handle the image upload
            $imageoptionE = $request->file('image_upload_optionE');
            $imageNameoptionE = time() . '_' . $imageoptionE->getClientOriginalName();
            $imageoptionE->move(public_path('files/question'), $imageNameoptionE);
            $question->option_e = $imageNameoptionE; // Save the image name in the 'option_e' field of the question
        } elseif ($request->input('input_type_optionE') === 'reuse' && $request->filled('image_use_optionE')) {
            // If it's a reuse and the image_use_optionE field is provided, copy the selected image to public/files/question
            $selectedImageFilenameoptionE = $request->input('image_use_optionE');
            $selectedImagePathoptionE = storage_path('app/public/images/' . $selectedImageFilenameoptionE);
        
            if (File::exists($selectedImagePathoptionE)) {
                $imageNameoptionE = time() . '_' . $selectedImageFilenameoptionE;
                File::copy($selectedImagePathoptionE, public_path('files/question/' . $imageNameoptionE));
                $question->option_e = $imageNameoptionE;
            } else {
                // Handle the case when the selected image does not exist
                $question->option_e = null; // Set option_e to null or handle it as needed

                return redirect()->back()->with('error', 'Gambar yang dipilih tidak ada.');
            }
        }

        $question->is_correct = $request->input('radio_option');

        $isExample = $request->input('contoh', 0);
        $question->is_example = $isExample;

        $question->example_explanation = $request->input('penjelasan');

        $quiz->questions()->save($question);

        return back()->with('success', 'Pertanyaan berhasil ditambahkan');
    }



    // public function storeEssayImage(Request $request, $quizId)
    // {
    //     $quiz = Quiz::findOrFail($quizId);
    
    //     $this->validate($request, [
    //         'image' => 'required', // Adjust the allowed file types and maximum file size as needed
    //         'correct' => 'required',
    //     ]);
    
    //     $question = new Question;

    //     if ($request->hasFile('image')) {
    //         $uploadedFile = $request->file('image');
            
    //         if ($uploadedFile->isValid()) {
    //             $fileName = time() . '.' . $uploadedFile->getClientOriginalExtension();
                
    //             $uploadedFile->move(public_path('files/question'), $fileName);
                
    //             $question->image = $fileName;
    //         } else {
    //             return redirect()->back()->with('error', 'Invalid file. Please upload a valid image file.');
    //         }
    //     }        
    
    //     $question->question = $request->input('question');
    //     $question->is_correct = $request->input('correct');
    
    //     $quiz->questions()->save($question);
    
    //     return redirect()->route('essay.exam', ['quiz' => $quiz->id])->with('success', 'Data question berhasil ditambahkan');
    // }


    public function questionEdit(Request $request, $quiz)
    {
        $questionId = $request->input('id');
    
        // Retrieve the question from the database
        $question = Question::findOrFail($questionId);
    
        // Update the question with the new data
        $question->question = $request->input('question');
        
        // Handle image update (if provided)
        if ($request->hasFile('image')) {
            // Remove the old image file (if exists)
            if ($question->image && File::exists(public_path('files/question/' . $question->image))) {
                File::delete(public_path('files/question/' . $question->image));
                $question->image = null; // Reset the image column in the database
            }

            // Upload the new image file
            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $imageFileName = uniqid() . '.' . $extension;
            $imagePath = $imageFile->move(public_path('files/question'), $imageFileName);
            $question->image = $imageFileName;
        }

        // Define an array of option letters
        $optionLetters = ['a', 'b', 'c', 'd'];

        foreach ($optionLetters as $optionLetter) {
            $optionInputName = 'option_' . $optionLetter;

            // Handle option image update (if provided)
            if ($request->hasFile($optionInputName)) {
                // Remove the old image file (if exists)
                if ($question->$optionInputName && File::exists(public_path('files/question/' . $question->$optionInputName))) {
                    File::delete(public_path('files/question/' . $question->$optionInputName));
                    $question->$optionInputName = null; // Reset the option column in the database
                }

                // Upload the new option file
                $imageFile = $request->file($optionInputName);
                $extension = $imageFile->getClientOriginalExtension();
                $imageFileName = uniqid() . '.' . $extension;
                $imagePath = $imageFile->move(public_path('files/question'), $imageFileName);
                $question->$optionInputName = $imageFileName;
            } elseif ($request->has($optionInputName)) {
                // Handle case where image is not provided (user edits using text)
                $question->$optionInputName = $request->input($optionInputName);
            }
        }

        // Handle option_e image update (if provided)
        if ($request->hasFile('option_e')) {
            // Remove the old image file (if exists)
            if ($question->option_e && File::exists(public_path('files/question/' . $question->option_e))) {
                File::delete(public_path('files/question/' . $question->option_e));
                $question->option_e = null; // Reset the option_e column in the database
            }

            // Upload the new option_e file
            $imageFile = $request->file('option_e');
            $extension = $imageFile->getClientOriginalExtension();
            $imageFileName = uniqid() . '.' . $extension;
            $imagePath = $imageFile->move(public_path('files/question'), $imageFileName);
            $question->option_e = $imageFileName;
        } elseif ($request->has('option_e')) {
            // Handle case where image is not provided (user edits using text)
            $question->option_e = $request->input('option_e');
        }
    
        // Get the selected radio button value for is_correct
        $isCorrectValue = $request->input('is_correct');
    
        // Set the is_correct value for the selected radio button
        $question->is_correct = $isCorrectValue;

        $question->example_explanation = $request->input('example_explanation');
    
        // Save the question model
        $question->save();
    
        // Return a response indicating success
        return response()->json(['message' => 'Pertanyaan berhasil diperbarui']);
    }


    public function essayEdit(Request $request, $quiz)
    {
        $questionId = $request->input('id');

        // Retrieve the question from the database
        $question = Question::find($questionId);
        
        if ($question) {
            // Question found, perform the update
            $question->question = $request->input('question');
            $question->is_correct = $request->input('is_correct');
            $question->example_explanation = $request->input('example_explanation');
        
            // Handle image update (if provided)
            if ($request->hasFile('image')) {
                // Remove the old image file (if exists)
                if ($question->image && File::exists(public_path('files/question/' . $question->image))) {
                    File::delete(public_path('files/question/' . $question->image));
                    $question->image = null; // Reset the image column in the database
                }
        
                // Upload the new image file
                $imageFile = $request->file('image');
                $extension = $imageFile->getClientOriginalExtension();
                $imageFileName = uniqid() . '.' . $extension;
                $imagePath = $imageFile->move(public_path('files/question'), $imageFileName);
                $question->image = $imageFileName;

            }
        
            // Save the question model
            $question->save();
        
            // Return a response indicating success
            return response()->json(['message' => 'Pertanyaan berhasil diperbarui']);
        } else {
            // Question not found, handle the situation (e.g., show an error message)
            return response()->json(['message' => 'Pertanyaan tidak ditemukan'], 404);
        }
    }        
     

    public function questionDelete(Request $request, $quiz)
    {
        $questionId = $request->input('id');
    
        // Find the question by its ID
        $question = Question::find($questionId);
    
        if (!$question) {
            return response()->json(['error' => 'Pertanyaan tidak ditemukan'], 404);
        }
    
        // Get the quiz ID before deleting the question
        $quizId = $question->quiz_id;
    
        // Delete the question
        $question->delete();
    
        // Delete associated files
        File::delete([
            'files/question/' . $question->image,
            'files/question/' . $question->option_a,
            'files/question/' . $question->option_b,
            'files/question/' . $question->option_c,
            'files/question/' . $question->option_d,
            'files/question/' . $question->option_e,
        ]);
    
        return redirect()->route('detail.exam', ['quiz' => $quizId])->with('success', 'Pertanyaan berhasil dihapus');
    }
    
    

    public function questionBulkInsert(Request $request, Quiz $quiz)
    {
        $request->validate([
            'xlsx_file' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        try {
            $xlsxFile = $request->file('xlsx_file');
            $spreadsheet = IOFactory::load($xlsxFile->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            $questions = [];
            $firstRowSkipped = false;

            foreach ($worksheet->getRowIterator() as $row) {
                if (!$firstRowSkipped) {
                    $firstRowSkipped = true;
                    continue;
                }

                /** @var Row $row */
                $rowData = $row->getCellIterator();
                
                $questionData = [];
                foreach ($rowData as $cell) {
                    $questionData[] = $cell->getValue();
                }
                
                $questions[] = [
                    'quiz_id' => $quiz->id,
                    'question' => $questionData[0],
                    'option_a' => $questionData[1],
                    'option_b' => $questionData[2],
                    'option_c' => $questionData[3],
                    'option_d' => $questionData[4],
                    'option_e' => $questionData[5] ?? null,
                    'is_correct' => $questionData[6],
                    // If your XLSX file contains more columns, adjust the array keys accordingly.
                ];
            }

            // Insert the data using the `insert()` method for bulk insert
            DB::table('questions')->insert($questions);

            return redirect()->back()->with('success', 'Bulk insert berhasil diselesaikan!');
        } catch (ReaderException $e) {
            return redirect()->back()->with('error', 'Kesalahan membaca file XLSX.');
        }
    }

    
    public function essayBulkInsert(Request $request, Quiz $quiz)
    {
        $request->validate([
            'xlsx_file' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        try {
            $xlsxFile = $request->file('xlsx_file');
            $spreadsheet = IOFactory::load($xlsxFile->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            $questions = [];
            $firstRowSkipped = false;

            foreach ($worksheet->getRowIterator() as $row) {
                if (!$firstRowSkipped) {
                    $firstRowSkipped = true;
                    continue;
                }

                /** @var Row $row */
                $rowData = $row->getCellIterator();
                
                $questionData = [];
                foreach ($rowData as $cell) {
                    $questionData[] = $cell->getValue();
                }
                
                $questions[] = [
                    'quiz_id' => $quiz->id,
                    'question' => $questionData[0],
                    'is_correct' => $questionData[1],
                    // If your XLSX file contains more columns, adjust the array keys accordingly.
                ];
            }

            // Insert the data using the `insert()` method for bulk insert
            DB::table('questions')->insert($questions);

            return redirect()->back()->with('success', 'Bulk insert berhasil diselesaikan!');
        } catch (ReaderException $e) {
            return redirect()->back()->with('error', 'Kesalahan membaca file XLSX.');
        }
    }


    public function managerExamQuestion()
    {
        $quizzes = Quiz::oldest()->get();
        $editableContent = file_get_contents(public_path('edited_content.txt'));
    
        foreach ($quizzes as $quiz) {
            $quiz->questionCount = Question::where('quiz_id', $quiz->id)
            ->where('is_example', 0)
            ->count();      
        }
    
        return view('/pages/manager/managerExam', ['quizzes' => $quizzes, 'editableContent' => $editableContent]);
    }
    
    
    public function deleteQuestionsForQuiz($quizId)
    {
        Question::where('quiz_id', $quizId)
        ->where('is_example', 0)
        ->delete();    
        
        // Optionally, you can add a redirect or response after the deletion process.
        // For example:
        return back()->with('success', 'Semua pertanyaan telah dihapus.');
    }


    public function exampleExam(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = Question::select("*")
                ->from("questions")
                ->where('quiz_id', $id)
                ->where('is_example', 1)
                ->orderBy('id', 'asc')
                ->get();
    
            $data = $data->map(function ($question, $index) {
                $question->row_number = $index + 1;
                return $question;
            });
    
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    // Add your action buttons here
                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $quiz = Quiz::find($id);
        return view('/pages/admin/exampleExamQuestion', compact('quiz'));
    }


    public function essayExample(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = Question::select("*")
                ->from("questions")
                ->where('quiz_id', $id)
                ->where('is_example', 1)
                ->orderBy('id', 'asc')
                ->get();
    
            $data = $data->map(function ($question, $index) {
                $question->row_number = $index + 1;
                return $question;
            });
    
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    // Add your action buttons here
                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $quiz = Quiz::find($id);
        return view('/pages/admin/essayExampleQuestion', compact('quiz'));
    }

}    