<?php
// PHP code to prepare the data
$data = json_encode([
    'teenage_pregnancy_rate' => 15.2,
    'unemployment_rate' => 7.8,
    'population_growth' => 3.2
]);

// Python code embedded directly into PHP (using heredoc)
$pythonCode = <<<PYTHON
import sys
import json
from transformers import AutoTokenizer, AutoModelForCausalLM

# Load the tokenizer and model
tokenizer = AutoTokenizer.from_pretrained("meta-llama/Llama-3-8B-Instruct")
model = AutoModelForCausalLM.from_pretrained("meta-llama/Llama-3-8B-Instruct", device_map="auto")

# Read input data passed from PHP
input_data = json.loads(sys.argv[1])
prompt = f"Analyze the following barangay data and suggest actionable recommendations:\n\nData: {input_data}\n\nRecommendations:"

# Generate response
inputs = tokenizer(prompt, return_tensors="pt").to("cuda")
outputs = model.generate(**inputs, max_length=200)
response = tokenizer.decode(outputs[0], skip_special_tokens=True)

print(response)
PYTHON;

// Save the Python code to a temporary file
$tempFile = tempnam(sys_get_temp_dir(), 'python_script_');
file_put_contents($tempFile, $pythonCode);

// Execute the Python script
$command = escapeshellcmd("python $tempFile " . escapeshellarg($data));
$output = shell_exec($command);

// Clean up the temporary Python script
unlink($tempFile);

// Display the output from Python
echo "<h1>AI Recommendations</h1>";
echo nl2br(htmlspecialchars($output));
?>
