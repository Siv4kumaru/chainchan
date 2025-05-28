import os
from collections import defaultdict
from langchain_community.document_loaders import DirectoryLoader, PyPDFLoader
from langchain_core.documents import Document
from langchain.text_splitter import RecursiveCharacterTextSplitter
import re

def pdf_chunks_chain_chan(path='../pdf/', file='*.pdf'):
    # Load all pages
    loader = DirectoryLoader(path=path, glob=file, loader_cls=PyPDFLoader)
    all_docs = loader.load()  # each Document represents one page

    # Group pages by their source PDF
    docs_by_source = defaultdict(list)
    for doc in all_docs:
        source = doc.metadata.get("source", "unknown.pdf")
        docs_by_source[source].append(doc)

    # Regex pattern and text splitter
    pattern = r"(^\d+\.\s+.+?)(?=^\d+\.\s+|\Z)"
    compiled = re.compile(pattern, flags=re.MULTILINE | re.DOTALL)
    text_splitter = RecursiveCharacterTextSplitter(chunk_size=1000, chunk_overlap=100)

    final_chunks = []

    # Process each source file separately
    for source_pdf, docs in docs_by_source.items():
        max_pages = len(docs) + 1

        # Get file size in bytes and convert to MB
        file_path = os.path.join(path, os.path.basename(source_pdf))
        try:
            size_bytes = os.path.getsize(file_path)
            size_mb = round(size_bytes / (1024 * 1024), 4)  # rounded to 4 decimals
        except Exception as e:
            size_mb = None  # or 0, or -1, if file not found or error

        # Step 1: Combine pages and track positions
        full_text = ""
        page_index = []
        offset = 0

        for doc in docs:
            full_text += doc.page_content + "\n"
            page_index.append((offset, doc.metadata.get("page", "unknown")))
            offset += len(doc.page_content) + 1

        # Step 2: Apply regex
        matches = compiled.finditer(full_text)

        def get_page_number(char_index):
            for offset, page_num in reversed(page_index):
                if char_index >= offset:
                    return page_num
            return "unknown"

        # Step 3: Create chunks with headings
        matched_ranges = []
        for match in matches:
            heading_line = match.group(1).split('\n', 1)[0].strip()
            content = match.group(1).strip()
            start_pos = match.start(1)
            page_num = get_page_number(start_pos)

            matched_ranges.append((match.start(1), match.end(1)))

            small_chunks = text_splitter.split_text(content)
            for chunk in small_chunks:
                final_chunks.append(
                    Document(
                        page_content=chunk,
                        metadata={
                            "source": source_pdf,
                            "title": heading_line,
                            "page": page_num,
                            "is_public":True,
                            "max_pages": max_pages,
                            "size_mb": size_mb,
                        }
                    )
                )

        # Step 4: Handle unmatched content
        covered = [0] * len(full_text)
        for start, end in matched_ranges:
            for i in range(start, end):
                covered[i] = 1

        current = ""
        start_pos = None

        for i, char in enumerate(full_text):
            if not covered[i]:
                if start_pos is None:
                    start_pos = i
                current += char
            else:
                if current.strip():
                    page_num = get_page_number(start_pos)
                    chunks = text_splitter.split_text(current.strip())
                    for chunk in chunks:
                        final_chunks.append(
                            Document(
                                page_content=chunk,
                                metadata={
                                    "source": source_pdf,
                                    "title": "No heading",
                                    "page": page_num,
                                    "max_pages": max_pages,
                                    "size_mb": size_mb,
                                    "is_public": True
                                }
                            )
                        )
                current = ""
                start_pos = None

        # Handle leftover text at the end if any
        if current.strip():
            page_num = get_page_number(start_pos)
            chunks = text_splitter.split_text(current.strip())
            for chunk in chunks:
                final_chunks.append(
                    Document(
                        page_content=chunk,
                        metadata={
                            "source": source_pdf,
                            "title": "No heading",
                            "page": page_num ,
                            "max_pages": max_pages ,
                            "size_mb": size_mb,
                            "is_public": True
                        }
                    )
                )

    return final_chunks

# fn=pdf_chunks_chain_chan("../pdf/","*.pdf")
# # Final output
# print(f"Total chunks: {len(fn)}")
# for i, doc in enumerate(fn, 1):
#     print(f"Chunk {i} (Page {doc.metadata}):")
#     print(doc.page_content)
#     print("-" * 40)
