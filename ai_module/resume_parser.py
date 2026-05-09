import sys, json, re
import PyPDF2

def extract_skills_from_resume(file_path):
    text = ""
    try:
        # ✅ Read PDF file
        if file_path.endswith(".pdf"):
            with open(file_path, "rb") as f:
                reader = PyPDF2.PdfReader(f)
                for page in reader.pages:
                    text += page.extract_text() or ""
        else:
            text = "Unsupported file format. Please upload PDF."

        # ✅ Define skill keywords (expandable list)
        skills_list = [
            "Python","Java","C++","SQL","Excel","Communication","HR","Sales",
            "Marketing","Data Analysis","Leadership","Management","Finance",
            "Teaching","Design","Recruitment","Customer Service","Project Management"
        ]

        found_skills = [skill for skill in skills_list if re.search(r"\b"+skill+r"\b", text, re.IGNORECASE)]
        return found_skills

    except Exception as e:
        # ✅ Return error message if parsing fails
        return {"error": str(e)}

if __name__ == "__main__":
    try:
        resume_path = sys.argv[1]
        skills = extract_skills_from_resume(resume_path)

        # ✅ Always return valid JSON
        if isinstance(skills, dict) and "error" in skills:
            print(json.dumps(skills))
        else:
            print(json.dumps({"extracted_skills": skills}))
    except Exception as e:
        print(json.dumps({"error": str(e)}))
